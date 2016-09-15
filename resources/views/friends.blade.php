@extends('layouts.app')

@section('site_title')

@section('content')
    <div class="container">

        @if(Auth::guest())
            <vk-auth></vk-auth>
        @else
            {{--<audio v-ref:audio></audio>--}}
            <div class="row">
                <div class="col s12 m4">
                    <friends v-ref:friends></friends>
                </div>

                <div class="col s12 m8">
                    qw
                </div>
            </div>
        @endif

    </div>
@endsection
