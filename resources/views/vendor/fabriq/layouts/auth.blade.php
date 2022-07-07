<!doctype html>
<html lang="{{ app()->getLocale() }}">
@include('vendor.fabriq._partials.head')

<body class="bg-gradient-to-r from-royal-900 to-royal-700">
    <main class="">
        <div class="flex items-start justify-center min-h-screen py-12 sm:pt-36">
            @yield('content')
        </div>
    </main>
</body>

</html>
