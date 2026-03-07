@props([
    'items' => [],
    'columns' => 4,
    'class' => '',
])

@if(count($items) > 0)
    <div class="feature-grid {{ $class }}" {{ $attributes }}>
        <div class="row g-4">
            @foreach($items as $index => $item)
                <div class="feature-grid__item col-12 col-sm-6 col-md-6 col-lg-{{ 12 / min($columns, 4) }}" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                    <div class="card card-rounded card-psychological h-100 border-0 shadow-soft feature-grid__card">
                        <div class="card-body card-padding">
                            @if(!empty($item['icon']))
                                <div class="feature-grid__icon rounded-3 d-flex align-items-center justify-content-center text-white mb-3" style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--color-teal-600) 0%, var(--color-soft-blue-500) 100%);">
                                    <i class="bi {{ $item['icon'] }} fs-4"></i>
                                </div>
                            @endif
                            @if(!empty($item['title']))
                                <h3 class="h5 fw-semibold text-psychological-primary mb-2">{{ $item['title'] }}</h3>
                            @endif
                            @if(!empty($item['description']))
                                <p class="text-stone-700 mb-0" style="line-height: 1.7;">{{ $item['description'] }}</p>
                            @endif
                            {{ $item['slot'] ?? '' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
