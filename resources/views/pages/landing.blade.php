@extends('layouts.guest')

@section('content')
    {{-- Trust line (optional) + Hero --}}
    <x-hero-section>
        <x-slot:trustLine>Trusted by clients seeking compassionate mental wellness</x-slot:trustLine>
        <x-slot:heading>Your Mind Aid</x-slot:heading>
        <x-slot:lead>Compassionate mental health care for your journey to wellness</x-slot:lead>
        <x-slot:ctas>
            <a href="{{ route('appointment.book') }}" class="btn btn-gradient-primary btn-lg px-5 py-3 shadow-soft-lg">
                <i class="bi bi-calendar-check me-2"></i>Book Appointment
            </a>
            <a href="#about" class="btn btn-gradient-outline btn-lg px-5 py-3">
                <i class="bi bi-info-circle me-2"></i>Learn More
            </a>
            <a href="{{ route('shop.products') }}" class="btn btn-gradient-outline btn-lg px-5 py-3">
                <i class="bi bi-bag me-2"></i>View Products
            </a>
            <a href="{{ route('articles.public.index') }}" class="btn btn-gradient-outline btn-lg px-5 py-3">
                <i class="bi bi-journal-text me-2"></i>Read Articles
            </a>
        </x-slot:ctas>
    </x-hero-section>

    {{-- About Us: section header + Mission/Vision grid --}}
    <section id="about" class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 public-section-title">About Us</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto">
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

    {{-- Our Expert Team --}}
    <section id="doctors" class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-2 position-relative">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 public-section-title">Our Expert Team</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto">
                    Meet our dedicated team of mental health professionals
                </p>
            </div>
            <div class="row g-4">
                @for($i = 1; $i <= 3; $i++)
                    <div class="col-12 col-md-4" data-aos="fade-up" data-aos-delay="{{ ($i - 1) * 100 }}">
                        <div class="card card-rounded card-psychological h-100 text-center border-0 shadow-soft">
                            <div class="card-body p-4 p-md-5">
                                <div class="rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center text-white display-6 fw-bold shadow-soft-lg" style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--color-teal-600) 0%, var(--color-soft-blue-500) 100%);">
                                    <span style="font-size: 2.5rem;">D{{ $i }}</span>
                                </div>
                                <h3 class="h4 font-semibold text-psychological-primary mb-2">{{ ['Dr. Sarah Johnson', 'Dr. Michael Chen', 'Dr. Emily Rodriguez'][$i - 1] }}</h3>
                                <p class="text-teal-700 fw-semibold mb-3">{{ ['Licensed Psychologist', 'Licensed Psychiatrist', 'Licensed Therapist'][$i - 1] }}</p>
                                <p class="text-stone-600 mb-0" style="line-height: 1.7;">
                                    {{ ['Specializes in anxiety and depression treatment', 'Expert in child and adolescent mental health', 'Focuses on trauma and PTSD recovery'][$i - 1] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- Shop Our Products CTA --}}
    <x-cta-section class="px-3 px-md-4 px-lg-5 bg-white position-relative">
        <x-slot:heading>Shop Our Products</x-slot:heading>
        <x-slot:lead>Browse wellness and self-care products to support your journey.</x-slot:lead>
        <x-slot:cta>
            <a href="{{ route('shop.products') }}" class="btn btn-gradient-primary btn-lg px-5 py-3 shadow-soft-lg">
                <i class="bi bi-bag me-2"></i>View All Products
            </a>
        </x-slot:cta>
    </x-cta-section>

    {{-- Featured Articles --}}
    @if ($featuredArticles->count() > 0)
    <section class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 public-section-title">Featured Articles</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto">
                    Explore our expert insights on mental health and wellness
                </p>
            </div>
            <div class="row g-4 mb-4">
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

    {{-- Latest Articles --}}
    @if ($latestArticles->count() > 0)
    <section class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-2 position-relative">
        <div class="container-fluid">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="h1 public-section-title">Latest Articles</h2>
                <p class="h5 public-section-lead text-stone-600 mx-auto">Recent insights from our team</p>
            </div>
            <div class="row g-4">
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

    {{-- Digital Wellbeing CTA --}}
    <x-cta-section class="px-3 px-md-4 px-lg-5 bg-white position-relative" id="wellbeing">
        <x-slot:heading>Digital Wellbeing</x-slot:heading>
        <x-slot:lead>Simple practices for a healthier relationship with technology: screen time awareness, mindfulness prompts, and daily tips.</x-slot:lead>
        <x-slot:cta>
            <a href="{{ route('wellbeing.public') }}" class="btn btn-gradient-outline btn-lg px-4 py-2">
                Explore Digital Wellbeing <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </x-slot:cta>
    </x-cta-section>

    {{-- Optional: Testimonials (empty for now; add items when data exists) --}}
    <x-testimonial-cards :items="[]" />

    {{-- Book an Appointment --}}
    <section id="appointment" class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-3 position-relative">
        <div class="container-fluid" style="max-width: 896px;">
            <x-cta-section class="p-0 mb-4 mb-md-5">
                <x-slot:heading>Book an Appointment</x-slot:heading>
                <x-slot:lead>Request an appointment with our mental health professionals. We'll contact you to confirm your preferred date and time.</x-slot:lead>
            </x-cta-section>
            <div class="card card-rounded card-psychological form-card-rounded shadow-soft-lg" data-aos="fade-up">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('appointment-request.store') }}">
                        @csrf
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-md-6">
                                <x-input type="text" name="first_name" label="First Name" value="{{ old('first_name') }}" required :error="$errors->first('first_name')" />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-input type="text" name="last_name" label="Last Name" value="{{ old('last_name') }}" required :error="$errors->first('last_name')" />
                            </div>
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-md-6">
                                <x-input type="email" name="email" label="Email Address" value="{{ old('email') }}" required :error="$errors->first('email')" />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-input type="tel" name="phone" label="Phone Number" value="{{ old('phone') }}" :error="$errors->first('phone')" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="session_mode" class="form-label text-stone-700 fw-semibold">Session Mode</label>
                            <select id="session_mode" name="session_mode" class="form-select @error('session_mode') is-invalid @enderror" style="transition: border-color 0.3s ease;">
                                <option value="">Select mode</option>
                                <option value="in_person" {{ old('session_mode') == 'in_person' ? 'selected' : '' }}>In-person</option>
                                <option value="online" {{ old('session_mode') == 'online' ? 'selected' : '' }}>Online</option>
                            </select>
                            @error('session_mode')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="address" class="form-label text-stone-700 fw-semibold">Address</label>
                            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Your address (optional)" style="transition: border-color 0.3s ease;">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="preferred_date" class="form-label text-stone-700 fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required class="form-control @error('preferred_date') is-invalid @enderror" style="transition: border-color 0.3s ease;" />
                                @error('preferred_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="preferred_time" class="form-label text-stone-700 fw-semibold">Preferred Time</label>
                                <select id="preferred_time" name="preferred_time" class="form-select @error('preferred_time') is-invalid @enderror" style="transition: border-color 0.3s ease;">
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
                        <div class="mb-4">
                            <label for="notes" class="form-label text-stone-700 fw-semibold">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Any additional information you'd like to share (optional)" style="transition: border-color 0.3s ease;">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient-primary btn-lg px-5 py-3 shadow-soft-lg">
                                <i class="bi bi-send me-2"></i>Submit Appointment Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="public-section px-3 px-md-4 px-lg-5 bg-gradient-section-1 position-relative">
        <div class="container-fluid" style="max-width: 896px;">
            <x-cta-section class="p-0 mb-4 mb-md-5">
                <x-slot:heading>Get in Touch</x-slot:heading>
                <x-slot:lead>We're here to help. Send us a message and we'll respond as soon as possible.</x-slot:lead>
            </x-cta-section>
            <div class="card card-rounded card-psychological form-card-rounded shadow-soft-lg" data-aos="fade-up">
                <div class="card-body p-4 p-md-5">
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
                                <x-input type="text" name="name" label="Your Name" value="{{ old('name') }}" required :error="$errors->first('name')" />
                            </div>
                            <div class="col-12 col-md-6">
                                <x-input type="email" name="email" label="Your Email" value="{{ old('email') }}" required :error="$errors->first('email')" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input type="text" name="subject" label="Subject" value="{{ old('subject') }}" required :error="$errors->first('subject')" />
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label text-stone-700 fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" rows="6" required class="form-control @error('message') is-invalid @enderror" style="transition: border-color 0.3s ease;">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient-primary btn-lg px-5 py-3 shadow-soft-lg">
                                <i class="bi bi-envelope me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
