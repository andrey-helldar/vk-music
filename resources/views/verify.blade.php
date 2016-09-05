@extends('layouts.app')

@section('site_title')

@section('content')
    <main>
        <header>
            <topmenu></topmenu>
        </header>

        <div class="container">
            <vk-verify></vk-verify>
        </div>
    </main>
@endsection
