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

                    <form method="POST" action="{{ $configuration ? route('configuration.update', ['configuration' => $configuration->id]) : route('configuration.store') }}" enctype="multipart/form-data">
                        @csrf

                        @if ($configuration) @method('PUT') @endif

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="logo" class="col-form-label text-md-right">{{ __('Logo') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-image"></i>
                                        </span>
                                    </div>
                                    <input id="logo" type="file" accept="image/*" class="form-control @error('logo') is-invalid @enderror" name="logo" value="{{ old('logo') }}">
                                </div>

                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact" class="col-form-label text-md-right">{{ __('Email for Contact') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input id="contact" type="email" placeholder="Digite um email para contato" class="form-control @error('contact') is-invalid @enderror" name="contact" value="{{ old('contact') ?? ($configuration->contact ?? '') }}" autocomplete="contact" autofocus>
                                </div>

                                @error('contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="copyright" class="col-form-label text-md-right">{{ __('Copyright') }}</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-copyright"></i>
                                        </span>
                                    </div>
                                    <input id="copyright" type="text" placeholder="Digite um texto de copyright" class="form-control @error('copyright') is-invalid @enderror" name="copyright" value="{{ old('copyright') ?? ($configuration->copyright ?? '') }}" autocomplete="copyright">
                                </div>

                                @error('copyright')
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
