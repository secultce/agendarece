@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body py-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row justify-content-center">
                            <div class="col-md-11">
                                <label for="email" class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-11">
                                <label for="password" class="col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="input-group" x-data="{ visible: false }">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>

                                    <input id="password" aria-label="{{ __('Password') }}" aria-describedby="password-visible" x-bind:type="visible ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    <div class="input-group-append">
                                        <span class="input-group-text pointer-cursor" x-on:click="visible = !visible">
                                            <i x-bind:class="'fas fa-eye' + (visible ? '-slash' : '')"></i>
                                        </span>
                                    </div>
                                </div>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-11">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-11">
                                <button type="submit" class="btn btn-dark btn-block btn-lg rounded">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-11">
                                    <a class="btn btn-link btn-block text-dark" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
