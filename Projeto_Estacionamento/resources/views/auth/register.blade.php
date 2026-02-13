
@extends('layouts.master')

@section('title', 'Register')

@section('content')

<div class="flex justify-center items-center">
    <div class="card w-full max-w-md bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="text-2xl font-bold text-center mb-4">
                Create Account
            </h2>

            <form method="POST" action="/register" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div class="form-control">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Bruno Santos"
                        value="{{ old('name') }}"
                        class="input input-bordered w-full"
                        required
                    >
                    @error('name')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-control">
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="bruno.santos@example.com"
                        value="{{ old('email') }}"
                        class="input input-bordered w-full"
                        required
                    >
                    @error('email')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-control">
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        class="input input-bordered w-full"
                        required
                    >
                    @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-control">
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        placeholder="Confirmar Password"
                        class="input input-bordered w-full"
                        required
                    >
                </div>

                {{-- Button --}}
                <div class="form-control mt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        Register
                    </button>
                </div>

                {{-- Divider --}}
                <div class="divider">OR</div>

                <p class="text-center text-sm">
                    Already have an account?
                    <a href="/login" class="link link-primary">
                        Sign in
                    </a>
                </p>

            </form>
        </div>
    </div>
</div>

@endsection