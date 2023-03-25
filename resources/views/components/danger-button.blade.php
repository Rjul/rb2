<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger btn-outline-danger m-3']) }}>
    {{ $slot }}
</button>
