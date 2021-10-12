@extends('base::layouts.simple')

@section('title') Login @endsection

@section('content')

    <section class="material-half-bg">
        <div class="cover"></div>
    </section>

    <section class="login-content">
        <div class="logo">
            <h1>{{ config('app.name') }}</h1>
        </div>
        @if(session()->has('message'))
            <div class="alert-message-group">
                <ul class="alert-messages-ul list-unstyled alert alert-success">
                    <li><span>{{ session()->get('message') }}</span></li>
                </ul>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert-message-group">
                <ul class="alert-messages-ul list-unstyled alert alert-danger">
                    <li><span>{{ session()->get('error') }}</span></li>
                </ul>
            </div>
        @endif
        @if($errors->any())
            <div class="alert-message-group">
                <ul class="alert-messages-ul list-unstyled alert alert-danger">
                    {!! implode('', $errors->all('<li><span>:message</span></li>')) !!}
                </ul>
            </div>
        @endif
        <div class="login-box">
            <form class="login-form" action="{{ route('userauth.authenticate') }}" method="POST" role="form">
                @csrf
                <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>SIGN IN</h3>
                <div class="form-group">
                    <label class="control-label" for="email">Email Address</label>
                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" placeholder="Email address" autofocus value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">Password</label>
                    <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <div class="utility">
                        <div class="animated-checkbox">
                            <label>
                                <input type="checkbox" name="remember"><span class="label-text">Stay Signed in</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group btn-container">
                    <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN</button>
                </div>
            </form>
        </div>
    </section>

@endsection
