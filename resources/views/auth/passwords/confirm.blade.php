@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body py-5">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="form-group row justify-content-center">
                            <div class="col-md-10">
                                <label for="password" class="col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="input-group custom-shadow" x-data="{ visible: false }">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>

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
                                <button type="submit" class="btn btn-dark btn-block  btn-lg rounded">
                                    {{ __('Confirm Password') }}
                                </button>
                            </div>
                        </div>

                        <div class="form-group row justify-content-center mb-0">
                            <div class="col-md-10">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link btn-block text-dark" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
