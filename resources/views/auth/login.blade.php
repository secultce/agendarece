@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body py-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row justify-content-center">
                            <div class="col-md-10">
                                <label for="email" class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-10">
                                <label for="password" class="col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="input-group" x-data="{ visible: false }">
                                    <input id="password" aria-label="{{ __('Password') }}" aria-describedby="password-visible" x-bind:type="visible ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    <div class="input-group-append">
                                        <button class="btn btn-dark" type="button" x-on:click="visible = !visible">
                                            <i x-bind:class="'fas fa-eye' + (visible ? '-slash' : '')"></i>
                                        </button>
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
                            <div class="col-md-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-dark btn-block">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-10">
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
