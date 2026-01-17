@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8" data-aos="fade-up">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-stone-900">
                    Sign in to Your Mind Aid
                </h2>
                <p class="mt-2 text-center text-sm text-stone-600">
                    Access your dashboard
                </p>
            </div>
            
            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="space-y-4">
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
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-stone-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-stone-700">
                                Remember me
                            </label>
                        </div>
                    </div>
                </div>
                
                <div>
                    <x-button type="submit" variant="primary" size="lg" class="w-full">
                        Sign in
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
