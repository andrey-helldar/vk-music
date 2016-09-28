@extends('layouts.app')

@section('content')
    <header>
        <topmenu v-ref:top-menu></topmenu>
    </header>

    <loader-screen v-ref:loader-screen></loader-screen>

    <router-view></router-view>
@endsection
