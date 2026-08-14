@foreach($programme->emisions as $emision)@if($emision->media_type == 'audio' && $emision->active_at < now() && $emision->attachment->first())
@php
    $att = $emision->attachment->first();
    // Extension conditionnelle : les lignes migrées de 2020 pouvaient l'avoir DANS name
    // (colonne vide) — on n'ajoute jamais de « . » orphelin.
    $file = $att->name . (filled($att->extension) ? '.' . $att->extension : '');
    $enclosureUrl = $att->url !== null
        ? 'https://www.radiobastides.fr' . $att->url
        : 'https://www.radiobastides.fr/storage/public/emission/audio/' . trim($att->path, '/') . '/' . $file;
@endphp
        <item>
            <title>{{ $emision->name }}</title>
            <description>{!! Str::words(strip_tags(str_replace(['>', '&nbsp;'], ['> ', ' '], Str::limit($emision->description, 200)))) !!}</description>
            <itunes:explicit>no</itunes:explicit>
            <pubDate>{{ $emision->active_at }}</pubDate>
            <enclosure url="{{ $enclosureUrl }}" type="{{ $att->mime ?: 'audio/mpeg' }}" length="{{ $att->size ?: '' }}"></enclosure>
@if(!is_null($emision->duration))
            <itunes:duration>{!! str_contains($emision->duration, '.') || is_null($emision->duration) ? str_replace('.', ':', $emision->duration ) : $emision->duration . ':00'  !!}</itunes:duration>
@endif
            <guid>{{ $emision->canonicalUrl() }}</guid>
        </item>
@endif
@endforeach
