<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-outline-info m-3']) }} class="">
    {{ $slot }}
</button>
