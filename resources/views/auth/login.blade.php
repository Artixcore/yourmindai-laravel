@extends('layouts.guest')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5 px-3">
        <div class="w-100" style="max-width: 448px;" data-aos="fade-up">
            <div>
                <h2 class="mt-3 text-center h2 fw-bold text-stone-900">
                    Sign in to Your Mind Aid
                </h2>
                <p class="mt-2 text-center small text-stone-600">
                    Access your dashboard
                </p>
            </div>
            
            <form class="mt-5" method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="d-flex flex-column gap-3">
                    <x-input 
                        type="email" 
                        name="email" 
                        label="Email or Username" 
                        value="{{ old('email') }}"
                        required
                        :error="$errors->first('email')"
                    />
                    
                    <x-input 
                        type="password" 
                        name="password" 
                        label="Password" 
                        required
                        :error="$errors->first('password')"
                    />
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="form-check-input"
                            >
                            <label for="remember" class="form-check-label ms-2 small text-stone-700">
                                Remember me
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <x-button type="submit" variant="primary" size="lg" class="w-100">
                        Sign in
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
