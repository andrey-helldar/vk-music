<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--<!-- CSRF Token -->--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site_title', 'VK Music')</title>

    {{--<!--Import Google Icon Font-->--}}
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    {{--<!-- Styles -->--}}
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    {{--<!-- Scripts -->--}}
    <script>
        window.Laravel = {!! $LaravelApp !!};
    </script>
</head>
<body>
<header>
    <div class="container">
        <h1>
            <a href="/">
                <img src="{{ asset('images/logotype.svg') }}" alt="@yield('site_title', 'VK Music')" class="logotype">
            </a>
        </h1>
    </div>
</header>

{{-- Content --}}
@yield('content')
{{-- // Content --}}

@include('layouts.footer')

{{--<!-- JavaScripts -->--}}
<script src="{{ elixir('js/app.js') }}"></script>

</body>
</html>
