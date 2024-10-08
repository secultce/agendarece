@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body py-5 padding">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <figure>
                            <img src="{{ asset('images/icon-mirante.png') }}" class="mx-auto" width="100%">
                        </figure>

                        <div class="form-group row justify-content-center">
                            <div class="col-md-10">
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
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-primary btn-block  btn-lg rounded">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>

                        @if (Route::has('login'))
                            <div class="form-group row justify-content-center mb-0">
                                <div class="col-md-10">
                                    <a class="btn btn-link btn-block text-primary" href="{{ route('login') }}">
                                        {{ __('Already have an account?') }}
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
