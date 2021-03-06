<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('layouts/favicon')

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('interface.site.title') }}</title>

    {{-- Import Font --}}
    <link rel="stylesheet" href="{{ elixir('css/font.css') }}">

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">

    {{-- Scripts --}}
    <script>
        window.Laravel = {!! $Laravel !!};
    </script>
</head>
<body>

{{-- Content --}}
<div id="app">
    <app-component ref="app" auth="{{ (int)Auth::check() }}" error-description="{{ $error ?? '' }}"></app-component>
</div>
{{-- // Content --}}

{{-- JavaScripts --}}
<script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
