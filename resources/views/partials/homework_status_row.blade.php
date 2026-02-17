<div class="col-md-4">
        <small class="text-muted">Status</small>
        <div>
            @if($homework->status === 'completed')
                <span class="badge bg-success">Completed</span>
            @elseif($homework->status === 'in_progress')
                <span class="badge bg-primary">In Progress</span>
            @elseif($homework->status === 'cancelled')
                <span class="badge bg-secondary">Cancelled</span>
            @else
                <span class="badge bg-warning">{{ ucfirst($homework->status) }}</span>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <small class="text-muted">Frequency</small>
        <div class="fw-semibold">{{ ucfirst($homework->frequency) }}</div>
    </div>
    <div class="col-md-4">
        <small class="text-muted">Date Range</small>
        <div>
            {{ \Carbon\Carbon::parse($homework->start_date)->format('M d, Y') }}
            @if($homework->end_date)
                - {{ \Carbon\Carbon::parse($homework->end_date)->format('M d, Y') }}
            @endif
        </div>
    </div>
