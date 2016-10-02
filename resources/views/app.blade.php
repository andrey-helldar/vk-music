<!DOCTYPE html>
<html lang="en" id="app">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--<!-- CSRF Token -->--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site_title', 'VK Music')</title>

    {{--<!--Import Google Icon Font-->--}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    {{--<!-- Styles -->--}}
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    {{--<!-- Scripts -->--}}
    <script>
        window.Laravel = {!! $LaravelApp !!};
    </script>
</head>
<body>

{{-- Content --}}
<app-component ref:app auth="{{ Auth::check() }}"></app-component>
{{-- // Content --}}

{{--<!-- JavaScripts -->--}}
<script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
