@props([
    'class' => '',
    'background' => 'bg-gradient-hero',
])

<section class="hero-section position-relative min-vh-80 d-flex align-items-center justify-content-center px-3 px-md-4 px-lg-5 {{ $background }} {{ $class }}">
    <div class="hero-section__inner position-relative" style="z-index: 10;">
        <div class="hero-section__content text-center">
            @if(isset($trustLine) && trim($trustLine) !== '')
                <p class="hero-section__trust" data-aos="fade-up">
                    {{ $trustLine }}
                </p>
            @endif
            @if(isset($heading))
                <h1 class="hero-section__heading" data-aos="fade-up" data-aos-delay="50">
                    {{ $heading }}
                </h1>
            @endif
            @if(isset($lead))
                <p class="hero-section__lead" data-aos="fade-up" data-aos-delay="100">
                    {{ $lead }}
                </p>
            @endif
            @if(isset($ctas) && trim($ctas) !== '')
                <div class="hero-section__ctas" data-aos="fade-up" data-aos-delay="150">
                    {{ $ctas }}
                </div>
            @endif
        </div>
    </div>
    <!-- Decorative blobs (hidden from assistive tech) -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden hero-section__blobs" style="pointer-events: none; z-index: 1;" aria-hidden="true">
        <div class="position-absolute rounded-circle opacity-25 animate-blob hero-section__blob hero-section__blob--1"></div>
        <div class="position-absolute rounded-circle opacity-25 animate-blob animation-delay-2000 hero-section__blob hero-section__blob--2"></div>
        <div class="position-absolute rounded-circle opacity-25 animate-blob animation-delay-4000 hero-section__blob hero-section__blob--3"></div>
        <div class="position-absolute rounded-circle opacity-20 animate-blob animation-delay-1000 hero-section__blob hero-section__blob--4"></div>
    </div>
</section>
