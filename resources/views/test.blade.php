@extends('layouts.app')

@section('site_title')

@section('content')
    <div class="container loader-screen-hide">

        <div id="test" v-cloak>
            <router-view class="test"></router-view>
        </div>

    </div>
@endsection
