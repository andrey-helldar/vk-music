@extends('layouts.app')

@section('content')
    <header>
        <vue-topmenu v-ref:top-menu></vue-topmenu>
    </header>

    <vue-loader-screen v-ref:loader-screen></vue-loader-screen>

    <router-view></router-view>
@endsection
