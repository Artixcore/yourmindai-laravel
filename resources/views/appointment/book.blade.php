@extends('layouts.guest')

@section('title', 'Book Appointment - Your Mind Aid')

@section('content')
<section class="py-5 px-3 px-md-4 px-lg-5">
    <div class="container-fluid" style="max-width: 896px;">
        <div class="text-center mb-4">
            <h1 class="h2 fw-bold text-psychological-primary">Book an Appointment</h1>
            <p class="text-stone-600">
                @if($doctor)
                    Request an appointment with {{ $doctor->name ?? $doctor->full_name ?? 'your doctor' }}
                    @if($doctor->doctor_number)
                        <span class="badge bg-primary">ID: {{ $doctor->doctor_number }}</span>
                    @endif
                @else
                    Request an appointment with our mental health professionals.
                @endif
            </p>
        </div>

        <div class="card card-psychological shadow-sm">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4 mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show m-4 mb-0" role="alert">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('appointment-request.store') }}" class="ajax-form">
                    @csrf
                    @if($doctor)
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        <input type="hidden" name="doctor_number" value="{{ $doctor->doctor_number }}">
                    @elseif($doctors->isNotEmpty())
                        <div class="mb-4">
                            <label for="doctor_id" class="form-label fw-semibold">Select Doctor <span class="text-danger">*</span></label>
                            <select name="doctor_id" id="doctor_id" class="form-select" required>
                                <option value="">Select a doctor</option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}" {{ old('doctor_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name ?? $d->full_name ?? 'Doctor' }}
                                        @if($d->doctor_number) ({{ $d->doctor_number }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Session Mode</label>
                        <select name="session_mode" class="form-select">
                            <option value="">Select</option>
                            <option value="in_person" {{ old('session_mode') == 'in_person' ? 'selected' : '' }}>In-person</option>
                            <option value="online" {{ old('session_mode') == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                            <input type="date" name="preferred_date" id="preferred_date" class="form-control" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required>
                            @if($doctors->isNotEmpty() || $doctor)
                                <div id="appointment-calendar-wrap" class="mt-3 {{ $doctor ? '' : 'd-none' }}">
                                    <div class="small text-muted mb-2">Select a date — <span class="day-overloaded-dot d-inline-block rounded-circle bg-danger me-1" style="width:8px;height:8px;"></span> = fully booked</div>
                                    <div id="appointment-calendar" class="appointment-calendar border rounded p-2 bg-white"></div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Time</label>
                            <select name="preferred_time" class="form-select">
                                <option value="">Select</option>
                                @foreach(['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'] as $t)
                                    <option value="{{ $t }}" {{ old('preferred_time') == $t ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($t)->format('g:i A') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                        <a href="{{ url('/') }}#appointment" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if($doctors->isNotEmpty() || $doctor)
<style>
.appointment-calendar { font-size: 0.85rem; }
.appointment-calendar .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.appointment-calendar .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; }
.appointment-calendar .cal-dow { font-weight: 600; color: #6b7280; padding: 0.25rem 0; }
.appointment-calendar .cal-day { padding: 0.35rem; cursor: pointer; border-radius: 4px; }
.appointment-calendar .cal-day:hover:not(.other-month):not(.day-overloaded) { background: #e5e7eb; }
.appointment-calendar .cal-day.other-month { color: #d1d5db; cursor: default; }
.appointment-calendar .cal-day.day-overloaded { background-color: rgba(255, 0, 0, 0.2); border: 1px solid #dc2626; }
.appointment-calendar .cal-day.selected { background: #3b82f6; color: white; }
.appointment-calendar .cal-day.past { color: #9ca3af; cursor: not-allowed; }
</style>
<script>
(function() {
    var doctorId = {{ $doctor ? $doctor->id : 'null' }};
    var lastOverloaded = [];
    var calendarWrap = document.getElementById('appointment-calendar-wrap');
    var calendarEl = document.getElementById('appointment-calendar');
    var dateInput = document.getElementById('preferred_date');
    var doctorSelect = document.getElementById('doctor_id');
    if (!calendarEl || !dateInput) return;

    function getDoctorId() {
        if (doctorId) return doctorId;
        if (doctorSelect && doctorSelect.value) return parseInt(doctorSelect.value, 10);
        return null;
    }

    function renderCalendar(year, month, overloadedDates) {
        overloadedDates = overloadedDates || [];
        var first = new Date(year, month - 1, 1);
        var last = new Date(year, month, 0);
        var startPad = first.getDay();
        var daysInMonth = last.getDate();
        var today = new Date();
        today.setHours(0,0,0,0);

        var html = '<div class="cal-header">';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary" data-nav="-1">&laquo;</button>';
        html += '<span class="fw-semibold">' + first.toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) + '</span>';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary" data-nav="1">&raquo;</button>';
        html += '</div>';
        html += '<div class="cal-grid">';
        ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(function(d) { html += '<div class="cal-dow">' + d + '</div>'; });
        for (var i = 0; i < startPad; i++) {
            var prevMonthDay = new Date(year, month - 1, -(startPad - i - 1));
            html += '<div class="cal-day other-month">' + prevMonthDay.getDate() + '</div>';
        }
        for (var d = 1; d <= daysInMonth; d++) {
            var dateStr = year + '-' + String(month).padStart(2,'0') + '-' + String(d).padStart(2,'0');
            var isOverloaded = overloadedDates.indexOf(dateStr) >= 0;
            var dateObj = new Date(year, month - 1, d);
            var isPast = dateObj < today;
            var isSelected = dateInput.value === dateStr;
            var cls = 'cal-day';
            if (isOverloaded) cls += ' day-overloaded';
            if (isPast) cls += ' past';
            if (isSelected) cls += ' selected';
            html += '<div class="' + cls + '" data-date="' + dateStr + '">' + d + '</div>';
        }
        var remaining = 42 - startPad - daysInMonth;
        for (var j = 0; j < remaining; j++) {
            html += '<div class="cal-day other-month">' + (j + 1) + '</div>';
        }
        html += '</div>';
        calendarEl.innerHTML = html;

        calendarEl.querySelectorAll('.cal-day:not(.other-month):not(.past)').forEach(function(cell) {
            cell.addEventListener('click', function() {
                if (this.classList.contains('day-overloaded')) return;
                var d = this.getAttribute('data-date');
                if (d) { dateInput.value = d; renderCalendar(year, month, overloadedDates); }
            });
        });
        calendarEl.querySelectorAll('[data-nav]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var delta = parseInt(this.getAttribute('data-nav'), 10);
                month += delta;
                if (month > 12) { month = 1; year++; }
                if (month < 1) { month = 12; year--; }
                fetchAndRender(year, month);
            });
        });
    }

    function fetchAndRender(year, month) {
        var did = getDoctorId();
        if (!did) { lastOverloaded = []; renderCalendar(year, month, []); return; }
        var monthStr = year + '-' + String(month).padStart(2,'0');
        jQuery.get('{{ url("/appointments/doctor") }}/' + did + '/availability?month=' + monthStr)
            .done(function(data) {
                lastOverloaded = Array.isArray(data) ? data.map(function(x) { return x.date; }) : [];
                renderCalendar(year, month, lastOverloaded);
            })
            .fail(function() { lastOverloaded = []; renderCalendar(year, month, []); });
    }

    var now = new Date();
    var y = now.getFullYear(), m = now.getMonth() + 1;
    if (dateInput.value) {
        var p = dateInput.value.split('-');
        if (p.length === 3) { y = parseInt(p[0],10); m = parseInt(p[1],10); }
    }

    if (doctorSelect) {
        doctorSelect.addEventListener('change', function() {
            doctorId = null;
            var did = getDoctorId();
            if (did) {
                calendarWrap.classList.remove('d-none');
                fetchAndRender(y, m);
            } else {
                calendarWrap.classList.add('d-none');
            }
        });
    }

    if (getDoctorId()) {
        fetchAndRender(y, m);
    } else {
        renderCalendar(y, m, []);
    }

    dateInput.addEventListener('change', function() {
        if (dateInput.value) {
            var p = dateInput.value.split('-');
            if (p.length === 3) { y = parseInt(p[0],10); m = parseInt(p[1],10); renderCalendar(y, m, lastOverloaded); }
        }
    });
})();
</script>
@endif
@endsection
