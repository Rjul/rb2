

<div class="container mt-5">


    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-body shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</div>

