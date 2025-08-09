@extends('layouts.app')

@section('content')
<div class="bg-linear-to-b from-gray-800 to-sky-800 h-screen pt-[25vh]">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form card relative z-50 bg-white rounded shadow m-auto p-6 my-1/2" style="width:25vw;">

                <div class="card-body col-span-full">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            

                            <div class="col-md-6">
                                <input id="email" type="email" placeholder="Username" class="form-control shadow w-full @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            

                            <div class="col-md-6">
                                <input id="password" type="password" placeholder="Password" class="form-control shadow @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width:auto;">

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div> -->
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded w-full">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <!-- <a href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a> -->
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="animated-gradient-bg" style="display: none;"></div>
            <div class="animated-clouds" style="display: none;"></div>
            <canvas id="dot-particles-canvas" class="absolute inset-0 z-0"></canvas>
@endsection
