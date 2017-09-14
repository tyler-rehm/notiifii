@extends('layouts.app')

@section('title', 'Main page')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1>
                        Welcome to the {{ env('APP_NAME') }} Communication Platform.
                    </h1>
                    <small>
                        The one supercharged control center for all your communication needs.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
