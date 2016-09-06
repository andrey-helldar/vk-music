@extends('layouts.app')

@section('site_title')

@section('content')
    <main>
        <header>
            <topmenu></topmenu>
        </header>

        <div class="container">

            @if(count($errors->messages()))
                <div class="row">
                    <div class="col s12 m6 offset-m3 center-align">
                        <h3>Warning!</h3>

                        <ul class="collection left-align red-text text-darken-2">
                            @foreach($errors->messages()[0] as $error)
                                <li class="collection-item">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>

                        <a class="btn btn-large btn-primary waves-effect waves-light" href="{{ route('index') }}">
                            <i class="material-icons left">send</i>
                            Return to auth page
                        </a>
                    </div>
                </div>
            @else
                <vk-verify></vk-verify>
            @endif

        </div>
    </main>
@endsection
