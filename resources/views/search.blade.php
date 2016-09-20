@extends('layouts.app')

@section('site_title')

@section('content')
    <div class="container loader-screen-hide">

        @if(Auth::guest())
            <vk-auth></vk-auth>
        @else
            <search v-ref:search></search>
            <audio v-ref:audio></audio>
        @endif

    </div>
@endsection
