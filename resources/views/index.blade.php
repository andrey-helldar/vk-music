@extends('layouts.app')

@section('site_title')

@section('content')
    <div class="container">

        @if(Auth::guest())
            <vk-auth></vk-auth>
        @else
            <audios v-ref:audios></audios>
        @endif

    </div>
@endsection
