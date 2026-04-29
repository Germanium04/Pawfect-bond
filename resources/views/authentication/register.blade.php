@extends('authentication.view_login&register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-register">
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
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                        @if ($errors->any())
                            <div style="background:red; color:white; padding:10px; margin-bottom:10px;">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>

                    <div class="mt-3">
                        <p>Already have an account? <a href="{{ route('login') }}">Login here.</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection