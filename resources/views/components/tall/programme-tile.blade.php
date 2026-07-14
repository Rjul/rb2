@props(['programme'])

@php
    $img   = $programme->image ?: 'https://picsum.photos/seed/rb-p-' . $programme->id . '/500/700';
    $url   = route('list-programme', ['programme' => $programme]);
    $count = $programme->emisions_count ?? null;
@endphp

<a href="{{ $url }}"
   class="group relative flex aspect-[3/4] items-end overflow-hidden rounded-card shadow-sm transition duration-200 hover:-translate-y-1.5 hover:shadow-xl">
    <img src="{{ $img }}" alt="" loading="lazy"
         onerror="this.onerror=null;this.src='https://picsum.photos/seed/rb-p-{{ $programme->id }}/500/700'"
         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
    <span class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-navy/90"></span>
    <div class="relative w-full p-5 text-white">
        <div class="font-display text-2xl leading-tight">{{ $programme->name }}</div>
        @if (!is_null($count))
            <div class="mt-1 text-sm text-slate-300">{{ $count }} émission{{ $count > 1 ? 's' : '' }}</div>
        @endif
    </div>
</a>
