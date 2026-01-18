@extends('layouts.guest')

@section('content')
    <!-- Hero Section -->
    <section class="position-relative min-h-screen d-flex align-items-center justify-content-center px-3 px-md-4 px-lg-5 bg-gradient-guest">
        <div class="container-fluid text-center" data-aos="fade-up">
            <h1 class="display-1 display-md-2 fw-bold text-stone-900 mb-4 mb-md-5">
                Your Mind Aid
            </h1>
            <p class="h4 h5-md text-stone-700 mb-4 mb-md-5 mx-auto" style="max-width: 768px;">
                Compassionate mental health care for your journey to wellness
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="#contact" class="btn btn-primary btn-lg px-5 py-3 shadow hover-shadow-xl">
                    Get Started
                </a>
                <a href="#about" class="btn btn-outline-primary btn-lg px-5 py-3 hover-bg-teal-50">
                    Learn More
                </a>
            </div>
        </div>
        
        <!-- Decorative elements -->
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none; z-index: 0;">
            <div class="position-absolute rounded-circle mix-blend-multiply filter opacity-30 animate-blob" style="top: 5rem; left: 2.5rem; width: 18rem; height: 18rem; background-color: #ccfbf1;"></div>
            <div class="position-absolute rounded-circle mix-blend-multiply filter opacity-30 animate-blob animation-delay-2000" style="top: 10rem; right: 2.5rem; width: 18rem; height: 18rem; background-color: #e0e7ff;"></div>
            <div class="position-absolute rounded-circle mix-blend-multiply filter opacity-30 animate-blob animation-delay-4000" style="bottom: -2rem; left: 50%; transform: translateX(-50%); width: 18rem; height: 18rem; background-color: #a7f3d0;"></div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 py-md-5 px-3 px-md-4 px-lg-5 bg-white">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 fw-bold text-stone-900 mb-3">About Us</h2>
                <p class="h5 text-stone-600 mx-auto" style="max-width: 768px;">
                    Your Mind Aid is dedicated to providing exceptional mental health care with a focus on compassion, understanding, and evidence-based treatment.
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <x-card data-aos="fade-right">
                        <h3 class="h3 font-semibold text-stone-900 mb-3">Our Mission</h3>
                        <p class="text-stone-700 leading-relaxed mb-0">
                            To provide accessible, high-quality mental health services that empower individuals to achieve emotional wellness and lead fulfilling lives. We believe in treating each person with dignity, respect, and personalized care.
                        </p>
                    </x-card>
                </div>
                
                <div class="col-12 col-md-6">
                    <x-card data-aos="fade-left">
                        <h3 class="h3 font-semibold text-stone-900 mb-3">Our Vision</h3>
                        <p class="text-stone-700 leading-relaxed mb-0">
                            To be a leading mental health clinic that transforms lives through innovative treatment approaches, compassionate care, and a commitment to breaking down barriers to mental health services.
                        </p>
                    </x-card>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="py-5 py-md-5 px-3 px-md-4 px-lg-5 bg-gradient-guest">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 fw-bold text-stone-900 mb-3">Our Expert Team</h2>
                <p class="h5 text-stone-600 mx-auto" style="max-width: 768px;">
                    Meet our dedicated team of mental health professionals
                </p>
            </div>
            
            <div class="row g-4">
                @for($i = 1; $i <= 3; $i++)
                    <div class="col-12 col-md-4" data-aos="fade-up" data-aos-delay="{{ ($i - 1) * 100 }}">
                        <x-card>
                            <div class="text-center">
                                <div class="rounded-circle bg-teal-700 mx-auto mb-3 d-flex align-items-center justify-content-center text-white display-6 fw-bold" style="width: 96px; height: 96px;">
                                    D{{ $i }}
                                </div>
                                <h3 class="h4 font-semibold text-stone-900 mb-2">Dr. {{ ['Sarah Johnson', 'Michael Chen', 'Emily Rodriguez'][$i - 1] }}</h3>
                                <p class="text-teal-700 mb-3">Licensed {{ ['Psychologist', 'Psychiatrist', 'Therapist'][$i - 1] }}</p>
                                <p class="text-stone-600 small mb-0">
                                    {{ ['Specializes in anxiety and depression treatment', 'Expert in child and adolescent mental health', 'Focuses on trauma and PTSD recovery'][$i - 1] }}
                                </p>
                            </div>
                        </x-card>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 py-md-5 px-3 px-md-4 px-lg-5 bg-white">
        <div class="container-fluid" style="max-width: 896px;">
            <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
                <h2 class="h1 fw-bold text-stone-900 mb-3">Get in Touch</h2>
                <p class="h5 text-stone-600">
                    We're here to help. Send us a message and we'll respond as soon as possible.
                </p>
            </div>
            
            <x-card data-aos="fade-up">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <x-input 
                                type="text" 
                                name="name" 
                                label="Your Name" 
                                value="{{ old('name') }}"
                                required
                                :error="$errors->first('name')"
                            />
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <x-input 
                                type="email" 
                                name="email" 
                                label="Your Email" 
                                value="{{ old('email') }}"
                                required
                                :error="$errors->first('email')"
                            />
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <x-input 
                            type="text" 
                            name="subject" 
                            label="Subject" 
                            value="{{ old('subject') }}"
                            required
                            :error="$errors->first('subject')"
                        />
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label text-stone-700">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="6" 
                            required
                            class="form-control @error('message') is-invalid @enderror"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="text-center">
                        <x-button type="submit" variant="primary" size="lg" class="w-100 w-md-auto px-5">
                            Send Message
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </section>
@endsection
