@props(['label', 'title', 'emissions'])

@if ($emissions->isNotEmpty())
    <div class="grid grid-cols-[auto_1fr] items-start gap-7 py-2 pb-12">
        <div class="hidden rotate-180 font-display text-[38px] text-navy/15 [writing-mode:vertical-rl] md:block">{{ $label }}</div>
        <div>
            <h2 class="font-display text-[30px] leading-tight text-navy">{{ $title }}</h2>
            <div class="mt-4 h-1 w-14 rounded bg-green-l"></div>
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($emissions as $emision)
                    <x-tall.emission-card :emision="$emision" />
                @endforeach
            </div>
        </div>
    </div>
@endif
