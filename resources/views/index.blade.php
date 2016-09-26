@extends('layouts.app')

@section('site_title')

@section('content')
    <header>
        <topmenu v-ref:top-menu></topmenu>
    </header>

    <loader-screen v-ref:loader-screen></loader-screen>
    <div class="container"></div>
@endsection
