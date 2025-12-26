@extends('layouts.guest')

@section('title', 'Register')

@section('body-class', '')

@section('content')
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <!-- Left side image -->
                    <div
                        class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                            style="background-image: url('{{ asset('assets/img/illustrations/illustration-signup.jpg') }}'); background-size: cover;">
                        </div>
                    </div>
                    <!-- Registration form -->
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                        <div class="card card-plain">
                            <div class="card-header">
                                <h4 class="font-weight-bolder text-brand">Sign Up</h4>
                                <p class="mb-0">Enter your email and password to register</p>
                            </div>
                            <div class="card-body">
                                <form role="form" method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required autocomplete="name" autofocus>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required
                                            autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" required
                                            autocomplete="new-password">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-lg bg-gradient-dark bg-brand btn-lg w-100 mt-4 mb-0">
                                            Sign Up
                                        </button>
                                    </div>
                                </form>
                                <p class="mt-4 text-sm text-center">
                                    Already have an account?
                                    <a href="{{ route('login') }}"
                                        class="text-primary bg-brand-secondary text-gradient font-weight-bold">
                                        Sign in
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
