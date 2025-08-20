@extends('layouts.app')

@section('content')
@dd(phpinfo())
<div class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <img src="{{asset('login_css/images/present.png')}}" alt="Image" class="img-fluid">
      </div>
      <div class="col-md-6 ">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="mb-4">
              <h3>Welcome back!</h3>
              <p class="mb-4"><strong>Please Login to your account</strong></p>
            </div>
            <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}" onsubmit='show()'>
              @csrf
              <div class="form-group first">
                <label for="email">Email</label>
                <input  class="form-control" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
              </div>
              <div class="form-group last mb-3">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="********" name="password" required>
              </div>
              <!-- <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
                <br>
                <span class="ml-auto"><a  href="{{ route('password.request') }}" style="text-decoration:none;" onclick='show()' class="forgot-pass">Forgot Password</a></span> 
              </div> -->
              <div class="mb-3 align-items-left">
                <!-- <span class="ml-auto"><a  href="{{ route('password.request') }}" style="text-decoration:none;" onclick='show()' class="forgot-pass">Forgot Password?</a></span>  -->
                <!-- <p class="ml-auto">If you've forgotten your credentials? Click submit a ticket.</p> -->
                <p class="ml-auto" style="display: inline-grid;">If you’ve forgotten your credentials, please click on <a href="{{ route('password.request') }}" onclick='show()' class="forgot-pass">Forgot Password</a></p> 
              </div>
              @if($errors->any())
                <div class="form-group alert alert-danger alert-dismissable">
                  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                  <strong>{{$errors->first()}}</strong>
                </div>
              @endif
              <input type="submit" value="Log In" class="btn text-white btn-block btn-primary">
              <div class="d-flex mt-3 align-items-center">
                <a class="mx-auto" style="text-decoration:none;" href="https://ticketing.rico.com.ph/itd/" target="_blank" class="forgot-pass">Submit a Ticket</a>
              </div> 
            </form>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{-- @include('auth.register_employee') --}}
@endsection
