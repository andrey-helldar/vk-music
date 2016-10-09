<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site_title', 'VK Music')</title>

    {{-- Import Google Icon Font --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    {{-- Styles --}}
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    {{-- Scripts --}}
    <script>
        window.Laravel = {!! $LaravelApp !!};
    </script>
</head>
<body>

{{-- Content --}}
<div id="app">
    <app-component ref="app" auth="{{ (int)Auth::check() }}"></app-component>
</div>

@if(count($errors->messages()))
    <div v-cloak>
        <h3>Warning!</h3>

        <ul class="collection red-text text-darken-2">
            @foreach($errors->messages()[0] as $error)
                <li class="collection-item">
                    {{ $error }}
                </li>
            @endforeach
        </ul>

        <a class="btn btn-large btn-primary waves-effect waves-light" href="{{ route('index') }}">
            <i class="material-icons left">send</i>
            Return to auth page
        </a>
    </div>
@endif

{{-- // Content --}}

{{-- JavaScripts --}}
<script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>
