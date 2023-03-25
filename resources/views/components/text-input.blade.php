@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'input-box w-100 border-1 rounded-3 p-2 m-2']) !!}
>
