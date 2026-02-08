@extends('client.layout')

@section('title', 'Digital Wellbeing - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Digital Wellbeing</h4>
    <p class="text-muted mb-0 small">Tips and practices for a healthier relationship with technology</p>
</div>

<!-- Screen time awareness -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-phone me-2 text-primary"></i>
            Screen time awareness
        </h6>
        <ul class="mb-0 ps-3">
            <li>Set daily limits for social media and entertainment apps.</li>
            <li>Use your device’s built-in screen time or digital wellbeing tools to track usage.</li>
            <li>Schedule screen-free periods (e.g. meals, first hour after waking).</li>
            <li>Turn off non-essential notifications to reduce constant checking.</li>
        </ul>
    </div>
</div>

<!-- Mindfulness prompts -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-heart me-2 text-primary"></i>
            Mindfulness prompts
        </h6>
        <ul class="mb-0 ps-3">
            <li>Before opening an app, take one deep breath and ask: “Do I need this right now?”</li>
            <li>Practice a 30-second pause: notice your feet on the floor and your breath before reaching for your phone.</li>
            <li>End your day with 2 minutes of quiet—no screens—to help sleep and mood.</li>
        </ul>
    </div>
</div>

<!-- Tips -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-lightbulb me-2 text-primary"></i>
            Wellbeing tips
        </h6>
        <ul class="mb-0 ps-3">
            <li>Keep your bedroom screen-free when possible to support better sleep.</li>
            <li>Replace some scroll time with a short walk or stretch.</li>
            <li>Curate your feed: mute or unfollow accounts that increase stress or comparison.</li>
            <li>Use technology for connection (e.g. calls, video) rather than passive scrolling when you can.</li>
        </ul>
    </div>
</div>
@endsection
