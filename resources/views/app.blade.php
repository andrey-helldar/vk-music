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
{{--<app-component ref:app auth="{{ Auth::check() }}"></app-component>--}}
<div id="app">
    <h1>Hello App!</h1>
    <p>
        <!-- use router-link component for navigation. -->
        <!-- specify the link by passing the `to` prop. -->
        <!-- <router-link> will be rendered as an `<a>` tag by default -->
        <router-link to="/foo">Go to Foo</router-link>
        <router-link to="/bar">Go to Bar</router-link>
    </p>
    <!-- route outlet -->
    <!-- component matched by the route will render here -->
    <router-view></router-view>
</div>
{{-- // Content --}}

{{--<!-- JavaScripts -->--}}
<script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
