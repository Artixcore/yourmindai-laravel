@extends('layouts.guest')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-teal-50 via-indigo-50 to-teal-100">
        <div class="max-w-7xl mx-auto text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-7xl font-bold text-stone-900 mb-6">
                Your Mind Aid
            </h1>
            <p class="text-xl md:text-2xl text-stone-700 mb-8 max-w-3xl mx-auto">
                Compassionate mental health care for your journey to wellness
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contact" class="px-8 py-3 bg-teal-700 text-white rounded-lg hover:bg-teal-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Get Started
                </a>
                <a href="#about" class="px-8 py-3 border-2 border-teal-700 text-teal-700 rounded-lg hover:bg-teal-50 transition-all duration-200">
                    Learn More
                </a>
            </div>
        </div>
        
        <!-- Decorative elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-teal-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-mint-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000"></div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-stone-900 mb-4">About Us</h2>
                <p class="text-xl text-stone-600 max-w-3xl mx-auto">
                    Your Mind Aid is dedicated to providing exceptional mental health care with a focus on compassion, understanding, and evidence-based treatment.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8">
                <x-card data-aos="fade-right">
                    <h3 class="text-2xl font-semibold text-stone-900 mb-4">Our Mission</h3>
                    <p class="text-stone-700 leading-relaxed">
                        To provide accessible, high-quality mental health services that empower individuals to achieve emotional wellness and lead fulfilling lives. We believe in treating each person with dignity, respect, and personalized care.
                    </p>
                </x-card>
                
                <x-card data-aos="fade-left">
                    <h3 class="text-2xl font-semibold text-stone-900 mb-4">Our Vision</h3>
                    <p class="text-stone-700 leading-relaxed">
                        To be a leading mental health clinic that transforms lives through innovative treatment approaches, compassionate care, and a commitment to breaking down barriers to mental health services.
                    </p>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 to-teal-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-stone-900 mb-4">Our Expert Team</h2>
                <p class="text-xl text-stone-600 max-w-3xl mx-auto">
                    Meet our dedicated team of mental health professionals
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                @for($i = 1; $i <= 3; $i++)
                    <x-card data-aos="fade-up" data-aos-delay="{{ ($i - 1) * 100 }}">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-teal-700 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold">
                                D{{ $i }}
                            </div>
                            <h3 class="text-xl font-semibold text-stone-900 mb-2">Dr. {{ ['Sarah Johnson', 'Michael Chen', 'Emily Rodriguez'][$i - 1] }}</h3>
                            <p class="text-teal-700 mb-4">Licensed {{ ['Psychologist', 'Psychiatrist', 'Therapist'][$i - 1] }}</p>
                            <p class="text-stone-600 text-sm">
                                {{ ['Specializes in anxiety and depression treatment', 'Expert in child and adolescent mental health', 'Focuses on trauma and PTSD recovery'][$i - 1] }}
                            </p>
                        </div>
                    </x-card>
                @endfor
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-stone-900 mb-4">Get in Touch</h2>
                <p class="text-xl text-stone-600">
                    We're here to help. Send us a message and we'll respond as soon as possible.
                </p>
            </div>
            
            <x-card data-aos="fade-up">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <x-input 
                            type="text" 
                            name="name" 
                            label="Your Name" 
                            value="{{ old('name') }}"
                            required
                            :error="$errors->first('name')"
                        />
                        
                        <x-input 
                            type="email" 
                            name="email" 
                            label="Your Email" 
                            value="{{ old('email') }}"
                            required
                            :error="$errors->first('email')"
                        />
                    </div>
                    
                    <div class="mb-6">
                        <x-input 
                            type="text" 
                            name="subject" 
                            label="Subject" 
                            value="{{ old('subject') }}"
                            required
                            :error="$errors->first('subject')"
                        />
                    </div>
                    
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-stone-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="6" 
                            required
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200 @error('message') border-red-500 @enderror"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="text-center">
                        <x-button type="submit" variant="primary" size="lg" class="w-full md:w-auto px-8">
                            Send Message
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </section>
@endsection

<style>
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }
    
    .animate-blob {
        animation: blob 7s infinite;
    }
    
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
