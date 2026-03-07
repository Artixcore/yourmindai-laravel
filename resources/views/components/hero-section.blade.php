@props([
    'class' => '',
    'background' => 'bg-gradient-hero',
])

<section class="hero-section position-relative min-vh-80 d-flex align-items-center justify-content-center px-3 px-md-4 px-lg-5 {{ $background }} {{ $class }}">
    <div class="container-fluid position-relative hero-section__inner" style="z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8 text-center">
                @if(isset($trustLine) && trim($trustLine) !== '')
                    <p class="hero-section__trust text-uppercase small fw-medium text-body-secondary mb-4" data-aos="fade-up">
                        {{ $trustLine }}
                    </p>
                @endif
                @if(isset($heading))
                    <h1 class="hero-section__heading display-4 display-md-3 fw-bold text-psychological-primary mb-4" data-aos="fade-up" data-aos-delay="50" style="letter-spacing: -0.02em;">
                        {{ $heading }}
                    </h1>
                @endif
                @if(isset($lead))
                    <p class="hero-section__lead fs-5 text-stone-700 mb-5 mx-auto" data-aos="fade-up" data-aos-delay="100" style="max-width: 42rem; line-height: 1.65;">
                        {{ $lead }}
                    </p>
                @endif
                @if(isset($ctas) && trim($ctas) !== '')
                    <div class="d-flex flex-column flex-sm-row flex-wrap gap-3 justify-content-center" data-aos="fade-up" data-aos-delay="150">
                        {{ $ctas }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Decorative blobs -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden hero-section__blobs" style="pointer-events: none; z-index: 1;">
        <div class="position-absolute rounded-circle opacity-25 animate-blob hero-section__blob hero-section__blob--1"></div>
        <div class="position-absolute rounded-circle opacity-25 animate-blob animation-delay-2000 hero-section__blob hero-section__blob--2"></div>
        <div class="position-absolute rounded-circle opacity-25 animate-blob animation-delay-4000 hero-section__blob hero-section__blob--3"></div>
        <div class="position-absolute rounded-circle opacity-20 animate-blob animation-delay-1000 hero-section__blob hero-section__blob--4"></div>
    </div>
</section>
