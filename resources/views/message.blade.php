@extends('layouts.app')

@section('content')
<style>
    .main-title h1 {
        font-weight: 700;
        color: hsl(192, 73%, 29%);
        margin-bottom: 50px;
    }
    .button {
        margin-top: 50px;
    }
</style>
    <div class="container">
        <div class="d-flex align-items-center justify-content-center min-vh-100">
            <div class="text-center">
                <div class="image">
                    <img src="{{ asset('/images/nothing.png') }}" class="img-fluid">
                </div>        
                <div class="main-title">
                    <h1>We're Now on the Cloud!</h1>
                </div>

                <h6>
                    To ensure a faster and safe experience, W Pro has moved to the cloud. <br>
                    Click below to access the new system.
                </h6>

                <div class="button">
                    <a href="https://beta.hris.wgroup.space/" class="btn btn-info">Go to WPro Cloud</a>
                </div>
            </div>
        </div>
    </div>
@endsection
