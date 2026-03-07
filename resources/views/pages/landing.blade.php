@extends('layouts.guest')

@section('content')
    {{-- 2. Hero --}}
    <x-hero-section>
        <x-slot:trustLine>Trusted by clients seeking compassionate mental wellness</x-slot:trustLine>
        <x-slot:heading>Your Mind Aid</x-slot:heading>
        <x-slot:lead>Compassionate mental health care for your journey to wellness</x-slot:lead>
        <x-slot:ctas>
            <a href="{{ route('appointment.book') }}" class="hero-cta hero-cta--primary">
                <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>Book Appointment
            </a>
            <a href="#about" class="hero-cta hero-cta--secondary">Learn More</a>
        </x-slot:ctas>
    </x-hero-section>

    {{-- 3. About / mission snapshot --}}
    <section id="about" class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid landing-content">
            <div class="text-center section-intro" data-aos="fade-up">
                <h2 class="h1 public-section-title">About Us</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto" style="max-width: 42rem;">
                    Your Mind Aid is dedicated to providing exceptional mental health care with a focus on compassion, understanding, and evidence-based treatment.
                </p>
            </div>
            @php
                $aboutItems = [
                    [
                        'icon' => 'bi-heart-pulse',
                        'title' => 'Our Mission',
                        'description' => 'To provide accessible, high-quality mental health services that empower individuals to achieve emotional wellness and lead fulfilling lives. We believe in treating each person with dignity, respect, and personalized care.',
                    ],
                    [
                        'icon' => 'bi-eye',
                        'title' => 'Our Vision',
                        'description' => 'To be a leading mental health clinic that transforms lives through innovative treatment approaches, compassionate care, and a commitment to breaking down barriers to mental health services.',
                    ],
                ];
            @endphp
            <x-feature-grid :items="$aboutItems" :columns="2" />
        </div>
    </section>

    {{-- 4. Our Expert Team --}}
    <section id="doctors" class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-gradient-section-2 position-relative">
        <div class="container-fluid landing-content">
            <div class="text-center section-intro" data-aos="fade-up">
                <h2 class="h1 public-section-title">Our Expert Team</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto" style="max-width: 42rem;">
                    Meet our dedicated team of mental health professionals
                </p>
            </div>
            @php
                $doctors = [
                    ['name' => 'Dr. Sarah Johnson', 'title' => 'Licensed Psychologist', 'specialty' => 'Specializes in anxiety and depression treatment', 'ctaText' => 'Book Now', 'ctaUrl' => route('appointment.book')],
                    ['name' => 'Dr. Michael Chen', 'title' => 'Licensed Psychiatrist', 'specialty' => 'Expert in child and adolescent mental health', 'ctaText' => 'Book Now', 'ctaUrl' => route('appointment.book')],
                    ['name' => 'Dr. Emily Rodriguez', 'title' => 'Licensed Therapist', 'specialty' => 'Focuses on trauma and PTSD recovery', 'ctaText' => 'Book Now', 'ctaUrl' => route('appointment.book')],
                ];
            @endphp
            <div class="row g-4 doctor-profile-card-row">
                @foreach($doctors as $index => $doctor)
                    <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <x-doctor-profile-card
                            :name="$doctor['name']"
                            :title="$doctor['title']"
                            :specialty="$doctor['specialty']"
                            :ctaText="$doctor['ctaText'] ?? null"
                            :ctaUrl="$doctor['ctaUrl'] ?? null"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Digital Wellbeing / services highlight --}}
    <section id="wellbeing" class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-white position-relative">
        <div class="container-fluid landing-content">
            <div class="row align-items-center landing-gap g-4 g-lg-5">
                <div class="col-12 col-lg-6" data-aos="fade-right">
                    <h2 class="h2 public-section-title mb-3">Digital Wellbeing</h2>
                    <p class="text-stone-700 mb-4" style="line-height: 1.8;">Simple practices for a healthier relationship with technology: screen time awareness, mindfulness prompts, and daily tips.</p>
                    <a href="{{ route('wellbeing.public') }}" class="btn btn-gradient-outline btn-lg px-4 py-3">
                        Explore Digital Wellbeing <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-12 col-lg-6" data-aos="fade-left">
                    <div class="rounded-4 overflow-hidden shadow-soft-lg bg-teal-50 d-flex align-items-center justify-content-center p-5" style="min-height: 200px;">
                        <i class="bi bi-phone text-teal-300" style="font-size: 4rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Products --}}
    <section id="products" class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid landing-content">
            <div class="card card-rounded card-psychological border-0 shadow-soft cta-block-card">
                <div class="card-body card-padding text-center">
                    <h2 class="h1 public-section-title mb-3" data-aos="fade-up">Shop Our Products</h2>
                    <p class="public-section-lead text-stone-600 mx-auto mb-4" data-aos="fade-up" data-aos-delay="50" style="max-width: 42rem;">
                        Browse wellness and self-care products to support your journey.
                    </p>
                    <div data-aos="fade-up" data-aos-delay="100">
                        <a href="{{ route('shop.products') }}" class="btn btn-gradient-primary btn-lg px-5 py-3 shadow-soft-lg">
                            <i class="bi bi-bag me-2"></i>View All Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Articles (optional: featured + latest when available) --}}
    @if ($featuredArticles->count() > 0)
    <section class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-gradient-section-2 position-relative">
        <div class="container-fluid landing-content">
            <div class="text-center section-intro" data-aos="fade-up">
                <h2 class="h1 public-section-title">Featured Articles</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto" style="max-width: 42rem;">
                    Explore our expert insights on mental health and wellness
                </p>
            </div>
            <div class="row landing-gap g-4 mb-5">
                @foreach ($featuredArticles as $article)
                    <div class="col-12 col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                            <x-article-card :article="$article" :showAuthor="true" class="h-100 card-rounded border-0 shadow-soft" />
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center">
                <a href="{{ route('articles.public.index') }}" class="btn btn-gradient-outline btn-lg px-5 py-3">
                    View All Articles <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    @if ($latestArticles->count() > 0)
    <section class="landing-page-section public-section landing-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid landing-content">
            <div class="text-center section-intro" data-aos="fade-up">
                <h2 class="h1 public-section-title">Latest Articles</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto" style="max-width: 42rem;">Recent insights from our team</p>
            </div>
            <div class="row landing-gap g-4">
                @foreach ($latestArticles as $article)
                    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                            <x-article-card :article="$article" :showAuthor="true" :compact="true" class="h-100 card-rounded border-0 shadow-soft" />
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 7. Appointment CTA + form (primary conversion) --}}
    <section id="appointment" class="landing-page-section public-section landing-section section-appointment px-3 px-md-4 px-lg-5 bg-gradient-section-3 position-relative">
        <div class="container-fluid landing-content" style="max-width: var(--landing-content-max, 72rem);">
            <x-cta-section class="p-0 section-intro mb-0">
                <x-slot:heading>Book an Appointment</x-slot:heading>
                <x-slot:lead>Request an appointment with our mental health professionals. We'll contact you to confirm your preferred date and time.</x-slot:lead>
            </x-cta-section>
            <div class="mt-4 mt-md-5 mb-0">
                <div class="appointment-form-card card-padding" data-aos="fade-up">
                    <div class="form-alerts">
                        @if(session('appointment_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('appointment_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('appointment-request.store') }}" class="landing-form landing-form--primary">
                        @csrf
                        <div class="appointment-form-groups">
                            <fieldset class="form-group mb-5 border-0 p-0">
                                <legend class="form-group-title">Name</legend>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <x-input type="text" name="first_name" label="First Name" value="{{ old('first_name') }}" required :error="$errors->first('first_name')" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-input type="text" name="last_name" label="Last Name" value="{{ old('last_name') }}" required :error="$errors->first('last_name')" />
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-5 border-0 p-0">
                                <legend class="form-group-title">Contact details</legend>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <x-input type="email" name="email" label="Email Address" value="{{ old('email') }}" required :error="$errors->first('email')" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-input type="tel" name="phone" label="Phone Number (optional)" value="{{ old('phone') }}" :error="$errors->first('phone')" />
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-5 border-0 p-0">
                                <legend class="form-group-title">Session details</legend>
                                <div class="mb-3">
                                    <label for="session_mode" class="form-label text-stone-700 fw-semibold">Session Mode</label>
                                    <select id="session_mode" name="session_mode" class="form-select @error('session_mode') is-invalid @enderror">
                                        <option value="">Select mode</option>
                                        <option value="in_person" {{ old('session_mode') == 'in_person' ? 'selected' : '' }}>In-person</option>
                                        <option value="online" {{ old('session_mode') == 'online' ? 'selected' : '' }}>Online</option>
                                    </select>
                                    @error('session_mode')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-3">
                                    <label for="address" class="form-label text-stone-700 fw-semibold">Address <span class="text-stone-500 fw-normal">(optional)</span></label>
                                    <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Your address">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-5 border-0 p-0">
                                <legend class="form-group-title">Preferred date & time</legend>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="preferred_date" class="form-label text-stone-700 fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                                        <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required class="form-control @error('preferred_date') is-invalid @enderror" />
                                        @error('preferred_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="preferred_time" class="form-label text-stone-700 fw-semibold">Preferred Time <span class="text-stone-500 fw-normal">(optional)</span></label>
                                        <select id="preferred_time" name="preferred_time" class="form-select @error('preferred_time') is-invalid @enderror">
                                            <option value="">Select a time</option>
                                            <option value="09:00" {{ old('preferred_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                            <option value="10:00" {{ old('preferred_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                            <option value="11:00" {{ old('preferred_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                            <option value="12:00" {{ old('preferred_time') == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                                            <option value="13:00" {{ old('preferred_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                            <option value="14:00" {{ old('preferred_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                            <option value="15:00" {{ old('preferred_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                            <option value="16:00" {{ old('preferred_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                            <option value="17:00" {{ old('preferred_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                        </select>
                                        @error('preferred_time')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-5 border-0 p-0">
                                <legend class="visually-hidden">Additional Notes</legend>
                                <label for="notes" class="form-label text-stone-700 fw-semibold">Additional Notes <span class="text-stone-500 fw-normal">(optional)</span></label>
                                <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Any additional information you'd like to share">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="text-center appointment-form-submit">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>Submit Appointment Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Contact --}}
    <section id="contact" class="landing-page-section public-section landing-section contact-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid landing-content" style="max-width: var(--landing-content-max, 72rem);">
            <div class="row align-items-start landing-gap g-4 g-lg-5">
                <div class="col-12 col-lg-5" data-aos="fade-up">
                    <h2 class="h2 public-section-title mb-3">Get in Touch</h2>
                    <p class="public-section-lead text-stone-600 mb-4">We're here to help. Send us a message and we'll respond as soon as possible.</p>
                    <div class="contact-section__info text-stone-700">
                        <p class="mb-2"><strong>Email</strong><br><a href="mailto:{{ config('app.contact_email') }}">{{ config('app.contact_email') }}</a></p>
                        <p class="mb-0"><strong>Phone</strong><br>{{ config('app.contact_phone') }}</p>
                    </div>
                </div>
                <div class="col-12 col-lg-7" data-aos="fade-up" data-aos-delay="50">
                    <div class="contact-form-card card-rounded card-padding">
                        <div class="form-alerts">
                            @if(session('contact_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('contact_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('contact.store') }}" class="landing-form landing-form--secondary">
                            @csrf
                            <fieldset class="border-0 p-0">
                                <legend class="visually-hidden">Send a message</legend>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <x-input type="text" name="name" label="Your Name" value="{{ old('name') }}" required :error="$errors->first('name')" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-input type="email" name="email" label="Your Email" value="{{ old('email') }}" required :error="$errors->first('email')" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <x-input type="text" name="subject" label="Subject" value="{{ old('subject') }}" required :error="$errors->first('subject')" />
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label text-stone-700 fw-semibold">Message <span class="text-danger">*</span></label>
                                    <textarea id="message" name="message" rows="5" required class="form-control @error('message') is-invalid @enderror" placeholder="How can we help?">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="contact-form-submit text-center text-md-start">
                                <button type="submit" class="btn btn-gradient-outline">
                                    <i class="bi bi-envelope me-2" aria-hidden="true"></i>Send Message
                                </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
