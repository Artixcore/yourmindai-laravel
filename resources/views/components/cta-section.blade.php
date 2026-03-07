@props([
    'class' => '',
])

<section class="cta-section public-section {{ $class }}" {{ $attributes }}>
    <div class="container-fluid cta-section__container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8 text-center">
                @if(isset($heading))
                    <h2 class="h1 public-section-title mb-3" data-aos="fade-up">{{ $heading }}</h2>
                @endif
                @if(isset($lead))
                    <p class="public-section-lead text-stone-600 mx-auto mb-4" data-aos="fade-up" data-aos-delay="50" style="max-width: 42rem;">
                        {{ $lead }}
                    </p>
                @endif
                @if(isset($cta) && trim($cta) !== '')
                    <div data-aos="fade-up" data-aos-delay="100">
                        {{ $cta }}
                    </div>
                @endif
                @if(isset($slot) && trim($slot) !== '')
                    <div class="cta-section__slot mt-4" data-aos="fade-up" data-aos-delay="100">
                        {{ $slot }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
