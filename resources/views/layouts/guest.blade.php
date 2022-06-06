<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ _vers('js/compiled.js') }}"></script>
    <script src="{{ _vers('js/app.js') }}"></script>
    <!-- Styles -->
    <link href="{{ _vers('css/compiled.css') }}" rel="stylesheet">
    <link href="{{ _vers('css/app.css') }}" rel="stylesheet">
    @stack('css')
</head>
<body class="@yield('bodyClass')">
    <main>
        <div class="main-container">
            @yield('content')
        </div>
    </main>
    @stack('modals')
    @stack('js')
    <script>
        @stack('js_script')
    </script>
</body>
</html>
