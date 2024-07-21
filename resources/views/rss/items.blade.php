@foreach($programme->emisions as $emision)@if($emision->media_type == 'audio' && $emision->active_at < now() && $emision->attachment->first())
        <item>
            <title>{{ $emision->name }}</title>
            <description>{!! Str::words(strip_tags(str_replace(['>', '&nbsp;'], ['> ', ' '], Str::limit($emision->description, 200)))) !!}</description>
            <itunes:explicit>no</itunes:explicit>
            <pubDate>{{ $emision->active_at }}</pubDate>
            <enclosure url="{{ $emision->attachment->first()->url !== null ? 'https://www.radiobastides.fr' . $emision->attachment->first()->url :
               'https://www.radiobastides.fr' . '/storage/public/emission/audio/' . $emision->attachment->first()->path . '/' . $emision->attachment->first()->name }}"
                type="{{ $emision->attachment->first()->mime ? $emision->attachment->first()->mime : "audio/mpeg" }}"
                length="{{ $emision->attachment->first()->size ? $emision->attachment->first()->size : (file_exists($emision->attachment->first()->url !== null ? $emision->attachment->first()->url :
                '/storage/public/emission/audio/' . $emision->attachment->first()->path . $emision->attachment->first()->name) ? filesize($emision->attachment->first()->url !== null ? $emision->attachment->first()->url :
                '/storage/public/emission/audio/' . $emision->attachment->first()->path . $emision->attachment->first()->name) : "") }}"></enclosure>
@if(!is_null($emision->duration))
            <itunes:duration>{!! str_contains($emision->duration, '.') || is_null($emision->duration) ? str_replace('.', ':', $emision->duration ) : $emision->duration . ':00'  !!}</itunes:duration>
@endif
            <guid>{{ route('view-emision', [ 'programme' => $emision->programme, 'emision' => $emision ]) }}</guid>
        </item>
@endif
@endforeach
