<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-glow btn-purple btn-hover-white h-12']) }}>
    {{ $slot }}
</button>
