@props(['value'])

<label {{ $attributes->merge(['class' => 'd-block fa-header-2 text-dark']) }}>
    {{ $value ?? $slot }}
</label>
