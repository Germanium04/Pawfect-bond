@extends('authentication.view_login&register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-login">
                <div class="card-header">
                    <div class="logo-container">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="logo-img">
                    </div>

                    <div class="header-text">
                        <h2>Paw-fect Bond</h2>
                        <p>Join us and find your perfect pet companion!</p>
                    </div>
                </div>

                <hr>

                <div class="card-body">
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <div class="form-group">
                            <label for="login">Username or Email</label>
                            <input type="text" class="form-control" id="login" name="login" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Login</button>

                        <div class="mt-3">
                            <p>Don't have an account? <a href="{{ route('register') }}">Register here.</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('others.forfun')
@endsection