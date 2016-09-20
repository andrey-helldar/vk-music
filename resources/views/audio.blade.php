@extends('layouts.app')

@section('site_title')

@section('content')
    <div class="container">

        @if(Auth::guest())
            <vk-auth></vk-auth>
        @else
            {{--<span class="audio-auto-loading hide"></span>--}}
            {{--<audio v-ref:audio></audio>--}}
            <router-view></router-view>
        @endif

    </div>
@endsection
