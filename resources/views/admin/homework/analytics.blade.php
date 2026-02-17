@extends('layouts.app')
@section('title', 'Homework Analytics')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Homework Analytics</h1></div>
<div class="card"><div class="card-body">
@foreach($completionRates ?? [] as $type => $rate)<p>{{ $type }}: {{ $rate }}%</p>@endforeach
@if(empty($completionRates))<p class="mb-0">No data.</p>@endif
</div></div>
@endsection
