@extends('layouts.auth')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update', ['user' => auth()->user()->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="name" class="col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input id="name" type="name" placeholder="Digite seu nome" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? ($user->name ?? '') }}" autocomplete="name" autofocus required>
                                </div>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="col-form-label text-md-right">{{ __('Email') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input id="email" type="email" placeholder="Digite um email para contato" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? ($user->email ?? '') }}" autocomplete="email" autofocus required>
                                </div>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="avatar" class="col-form-label text-md-right">{{ __('Avatar') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-image"></i>
                                        </span>
                                    </div>
                                    <input id="avatar" type="file" accept="image/*" class="form-control @error('avatar') is-invalid @enderror" name="avatar">
                                </div>

                                @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="password">Senha</label>
                                <div class="input-group" x-data="{ visible: false }">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                    
                                    <input name="password" class="form-control @error('password') is-invalid @enderror" x-bind:type="visible ? 'text' : 'password'" placeholder="Digite a senha">
                    
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

                            <div class="col-md-6">
                                <label for="password_confirmation">Confirmar Senha</label>
                                <div class="input-group" x-data="{ visible: false }">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                    
                                    <input name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" x-bind:type="visible ? 'text' : 'password'" placeholder="Cofirme a senha">
                    
                                    <div class="input-group-append">
                                        <span class="input-group-text pointer-cursor" x-on:click="visible = !visible">
                                            <i x-bind:class="'fas fa-eye' + (visible ? '-slash' : '')"></i>
                                        </span>
                                    </div>
                                </div>

                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row justify-content-end">
                            <div class="col-md-3 text-end">
                                <button type="submit" class="btn btn-primary btn-lg text-white btn-rounded">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
