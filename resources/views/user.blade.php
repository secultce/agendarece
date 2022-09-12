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

                    <user-data-table :auth-user="{{ json_encode(auth()->user()) }}"></user-data-table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
