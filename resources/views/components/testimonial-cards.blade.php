@props([
    'items' => [],
    'title' => '',
    'subtitle' => '',
])

@if(count($items) > 0)
    <section class="public-section testimonial-cards-section" {{ $attributes }}>
        <div class="container-fluid">
            @if($title || $subtitle)
                <div class="text-center mb-5" data-aos="fade-up">
                    @if($title)
                        <h2 class="h1 public-section-title">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="h5 public-section-lead text-stone-600 mx-auto">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
            <div class="row g-4">
                @foreach($items as $index => $item)
                    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                        <div class="card card-rounded card-psychological h-100 border-0 shadow-soft testimonial-card">
                            <div class="card-body p-4 p-md-5">
                                @if(!empty($item['quote']))
                                    <p class="testimonial-card__quote text-stone-700 mb-4" style="line-height: 1.75; font-style: italic;">
                                        "{{ $item['quote'] }}"
                                    </p>
                                @endif
                                @if(!empty($item['author']))
                                    <p class="fw-semibold text-psychological-primary mb-1">{{ $item['author'] }}</p>
                                @endif
                                @if(!empty($item['role']))
                                    <p class="small text-body-secondary mb-0">{{ $item['role'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
