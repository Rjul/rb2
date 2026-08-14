<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

use App\Models\Emision as Emission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\Concerns\Commentable;
use App\Models\Concerns\HasResolvedImage;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Platform\Dashboard;
use Orchid\Screen\AsSource;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;


class Emision extends Model
{
    use HasFactory, HasSlug, Attachable;
    use AsSource, Filterable, HasTags, Commentable, HasResolvedImage;

    const TYPE_TEXT = 'text';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    /**
     * Avalable type audio / video
     * @Todo voir pour le contenue text
     * @param string $type
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getLastByType(string $type = 'audio', int $limite = 4) {
        return self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->where('media_type', '=', $type)
            ->orderBy('active_at', 'desc')
            ->where('active_at', '<', now())
            ->where('emisions.is_active', true)
            ->where('programmes.is_active', true)
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
    }

    public static function getLastALaUne(int $limite = 4) {
        return self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->where('is_put_forward', true)
            ->where('active_at', '<', now())
            ->where('emisions.is_active', "=", true)
            ->where('programmes.is_active', true)
            ->orderBy('emisions.active_at', 'DESC')
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
    }

    public static function getLast(int $limite = 7)
    {
        return self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->orderBy('active_at', 'desc')
            ->where('active_at', '<', now())
            ->where('emisions.is_active', true)
            ->where('programmes.is_active', true)
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();
    }

    /**
     * Sélection « à la une » de la SEMAINE pour la newsletter hebdomadaire :
     * émissions mises en avant publiées sur les 7 derniers jours. Replis en
     * cascade pour ne jamais envoyer une newsletter vide :
     * à la une de la semaine → à la une (toutes dates) → dernières publiées.
     * Eager-load attachment + programme.group_programme (URL canonique/audio).
     */
    public static function getWeeklyHighlights(int $limite = 6)
    {
        $rel = ['attachment', 'programme.group_programme'];

        $week = self::join('programmes', 'emisions.programme_id', '=', 'programmes.id', 'inner')
            ->select('emisions.*')
            ->where('is_put_forward', true)
            ->whereBetween('active_at', [now()->subWeek(), now()])
            ->where('emisions.is_active', true)
            ->where('programmes.is_active', true)
            ->orderBy('emisions.active_at', 'DESC')
            ->orderBy('programmes.height')
            ->limit($limite)
            ->get();

        if ($week->isNotEmpty()) {
            return $week->load($rel);
        }

        $featured = self::getLastALaUne($limite);
        if ($featured->isNotEmpty()) {
            return $featured->load($rel);
        }

        return self::getLast($limite)->load($rel);
    }

    public function scopeWithAuthPermissions(Builder $builder): Builder
    {
        $programmes_id = [];
        $permisions = Auth()->user()->permissions;
        if (!Auth()->user()->roles->isEmpty()) {
            foreach (Auth()->user()->roles as $role) {
                $permisions = array_merge($permisions ?? [], $role->permissions);
            }
        }
        foreach ($permisions as $permision => $key) {
            if ($permision === "platform.programmes" && ($key === "1" || $key === true) ) {
                return $builder;

            } else if (str_contains($permision, 'platform.emission.') && ($key === "1" || $key === true) ) {
                preg_match('/platform\.emission\.([0-9]+)/', $permision, $matches);
                $programmes_id[] = $matches[1];
            }
        }
        if (!empty($programmes_id)) {
            foreach ($programmes_id as $id) {
                $builder->orWhere('programme_id', $id);
            };
            return $builder;
        }
        $builder->where('programme_id', 999);

        return $builder;
    }

    /**
     * Get the group Programme for the blog post.
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * URL streamable du fichier audio (disque local public `emission_audio`),
     * ou null si l'émission n'est pas de type audio / sans pièce jointe.
     * Utilise la relation `attachment` déjà chargée si possible (évite le N+1).
     */
    public function audioUrl(): ?string
    {
        if ($this->media_type !== self::TYPE_AUDIO) {
            return null;
        }

        $attachment = $this->relationLoaded('attachment')
            ? $this->getRelation('attachment')->firstWhere('group', 'audio')
            : $this->attachment('audio')->first();

        if (! $attachment) {
            return null;
        }

        return rescue(
            fn () => Storage::disk('emission_audio')
                ->url(trim($attachment->path, '/') . '/' . self::attachmentFileName($attachment)),
            null,
            false
        );
    }

    /**
     * Nom de fichier d'un attachment, défensif : n'ajoute « .extension » que si
     * elle est renseignée (les lignes migrées de 2020 avaient l'extension DANS
     * name et la colonne vide → « 90.mp3. » sinon).
     */
    private static function attachmentFileName($attachment): string
    {
        return $attachment->name
            . (filled($attachment->extension) ? '.' . $attachment->extension : '');
    }

    /**
     * URL streamable du fichier vidéo (disque distant `emission_video`, FTP),
     * ou null si l'émission n'est pas de type vidéo / sans pièce jointe.
     * Le disque FTP peut lever (throw=true) : encapsulé dans rescue().
     */
    public function videoUrl(): ?string
    {
        if ($this->media_type !== self::TYPE_VIDEO) {
            return null;
        }

        $attachment = $this->relationLoaded('attachment')
            ? $this->getRelation('attachment')->firstWhere('group', 'video')
            : $this->attachment('video')->first();

        if (! $attachment) {
            return null;
        }

        // L'attachement Orchid connaît son disque : on privilégie son url() ;
        // sinon on reconstruit sur le disque vidéo dédié.
        return rescue(
            fn () => $attachment->url
                ?: Storage::disk('emission_video')
                    ->url(trim($attachment->path, '/') . '/' . self::attachmentFileName($attachment)),
            null,
            false
        );
    }

    /**
     * URL canonique de la fiche (front v2). Les segments catégorie/programme
     * sont cosmétiques : la résolution se fait par l'id contenu dans le slug.
     */
    public function canonicalUrl(): string
    {
        $prog = $this->programme;

        return route('v2.emission', [
            'categorie'  => $prog?->group_programme?->slug ?: 'programmes',
            'programme'  => $prog?->slug ?: 'programme',
            'emission'   => $this->slug,
        ]);
    }

    /**
     * Résout une émission depuis le dernier segment d'URL.
     * 1) slug exact (cas courant, aucune ambiguïté, que le slug contienne l'id ou non) ;
     * 2) repli sur l'id en fin de slug si le slug est périmé (renommage) → self-healing 301.
     */
    public static function fromSlugId(?string $segment): ?self
    {
        $segment = (string) $segment;
        if ($segment === '') {
            return null;
        }

        if ($model = self::query()->where('slug', $segment)->first()) {
            return $model;
        }

        $id = (int) \Illuminate\Support\Str::afterLast($segment, '-');

        return $id > 0 ? self::query()->find($id) : null;
    }

    /**
     * Publiée = visible du public : active, datée dans le passé, programme actif.
     * Règle unique partagée par la fiche v2 (404/préversion) et le back-office.
     */
    public function isPublished(): bool
    {
        return (bool) $this->is_active
            && $this->active_at
            && ! \Illuminate\Support\Carbon::parse($this->active_at)->isFuture()
            && (bool) $this->programme?->is_active;
    }

    public function attachment(string $group = null, ?int $duration = null): MorphToMany
    {
        $query = $this->morphToMany(
            Dashboard::model(\App\Models\Attachment::class),
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }
        if ($duration !== null) {
            $query->where('duration', $duration);
        }

        return $query
            ->orderBy('sort');
    }
    public function attachments(string $group = null, ?int $duration = null): MorphToMany
    {
        $query = $this->morphToMany(
            Dashboard::model(\App\Models\Attachment::class),
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }
        if ($duration !== null) {
            $query->where('duration', $duration);
        }

        return $query
            ->orderBy('sort');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'programme_id',
        'user_id',
        'name',
        'description',
        'duration',
        'media_type',
        'is_put_forward',
        'image',
        'is_active',
        'active_at'
    ];


    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'id'])
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'programme_id'   => Where::class,
        'programme.name' => Like::class,
        'user_id'        => Where::class,
        'name'           => Like::class,
        'description'    => Like::class,
        'media_type'     => Where::class,
        'is_put_forward' => Where::class,
        'image'          => Like::class,
        'is_active'      => Where::class,
        'active_at'      => Where::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'programme_id',
        'user_id',
        'name',
        'description',
        'media_type',
        'is_put_forward',
        'image',
        'is_active',
        'active_at',
        'programme.name'
    ];
}
