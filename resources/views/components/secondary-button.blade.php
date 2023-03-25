<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary btn-outline-secondary']) }} >
    {{ $slot }}
</button>
