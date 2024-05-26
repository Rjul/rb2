@php echo '<?xml version="1.0" encoding="UTF-8"?>'@endphp
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
    <channel>
        <title>Podcast Dafna et ses zèbres</title>
        <itunes:owner>
            <itunes:email>{{ $programme->user->email }}</itunes:email>
        </itunes:owner>
        <itunes:author>{{ $programme->user->name }}</itunes:author>
        <description>{{ $programme->description }}</description>
        <itunes:image href="{{ base_path() }}/{{ $programme->image }}"/>
        <language>fr</language>
        <link>{{ route('list-programme', $programme) }}</link>
        @include('rss/items')
    </channel>
</rss>
