@extends('layouts.app')

@section('site_title')

@section('content')
    <main>
        <header>
            <topmenu></topmenu>
        </header>

        <div class="container">

            @if(Auth::guest())
                <vk-auth></vk-auth>
            @else
                <audios></audios>
            @endif
        </div>
    </main>
@endsection
