@props([
    'reverse' => false,
    'class' => '',
])

<section class="split-feature-section public-section {{ $class }}" {{ $attributes }}>
    <div class="container-fluid">
        <div class="row align-items-center g-4 g-lg-5 {{ $reverse ? 'flex-row-reverse' : '' }}">
            <div class="col-12 col-lg-6" data-aos="{{ $reverse ? 'fade-left' : 'fade-right' }}">
                @if(isset($heading))
                    <h2 class="h2 public-section-title mb-3">{{ $heading }}</h2>
                @endif
                @if(isset($content))
                    <div class="split-feature-section__content text-stone-700" style="line-height: 1.8;">
                        {{ $content }}
                    </div>
                @endif
                @if(isset($cta) && trim($cta) !== '')
                    <div class="mt-4">
                        {{ $cta }}
                    </div>
                @endif
            </div>
            @if(isset($image) && trim($image) !== '')
                <div class="col-12 col-lg-6" data-aos="{{ $reverse ? 'fade-right' : 'fade-left' }}">
                    <div class="split-feature-section__image-wrapper rounded-4 overflow-hidden shadow-soft-lg">
                        {{ $image }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
