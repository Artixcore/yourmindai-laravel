@props([
    'name' => '',
    'title' => '',
    'specialty' => '',
    'image' => null,
    'ctaText' => null,
    'ctaUrl' => null,
])

@php
    $initials = '';
    if ($name) {
        $parts = preg_split('/\s+/', trim($name), 2);
        $initials = isset($parts[1])
            ? mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1)
            : mb_substr($parts[0], 0, 2);
        $initials = strtoupper($initials);
    }
    $hasCta = ($ctaText && $ctaUrl);
@endphp

<article class="doctor-profile-card h-100 d-flex flex-column">
    <div class="doctor-profile-card__inner">
        <div class="doctor-profile-card__avatar-wrap">
            <div class="doctor-profile-card__avatar">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $name }}" class="doctor-profile-card__avatar-img" loading="lazy">
                @else
                    <span class="doctor-profile-card__initials">{{ $initials ?: '?' }}</span>
                @endif
            </div>
        </div>
        <div class="doctor-profile-card__body">
            @if($name)
                <h3 class="doctor-profile-card__name">{{ $name }}</h3>
            @endif
            @if($title)
                <p class="doctor-profile-card__title">{{ $title }}</p>
            @endif
            @if($specialty)
                <p class="doctor-profile-card__bio">{{ $specialty }}</p>
            @endif
            @if($hasCta)
                <a href="{{ $ctaUrl }}" class="doctor-profile-card__cta">{{ $ctaText }}</a>
            @endif
            {{ $slot ?? '' }}
        </div>
    </div>
</article>
