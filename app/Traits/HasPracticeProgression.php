<?php

namespace App\Traits;

use App\Models\PracticeProgression;
use Carbon\Carbon;

trait HasPracticeProgression
{
    /**
     * Get all practice progressions for this model.
     */
    public function practiceProgressions()
    {
        return $this->morphMany(PracticeProgression::class, 'progressionable');
    }

    /**
     * Get progressions by date range.
     */
    public function progressionsBetween(Carbon $startDate, Carbon $endDate)
    {
        return $this->practiceProgressions()
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->orderBy('progress_date')
            ->get();
    }

    /**
     * Get progressions monitored by source.
     */
    public function progressionsByMonitor(string $monitoredBy)
    {
        return $this->practiceProgressions()->where('monitored_by', $monitoredBy)->get();
    }

    /**
     * Get parent monitored progressions.
     */
    public function parentProgressions()
    {
        return $this->practiceProgressions()->where('monitored_by', 'parent')->get();
    }

    /**
     * Get self monitored progressions.
     */
    public function selfProgressions()
    {
        return $this->practiceProgressions()->where('monitored_by', 'self')->get();
    }

    /**
     * Get others monitored progressions.
     */
    public function othersProgressions()
    {
        return $this->practiceProgressions()->where('monitored_by', 'others')->get();
    }

    /**
     * Add progression entry.
     */
    public function addProgression(int $patientId, string $date, int $percentage, string $status, string $monitoredBy, ?int $monitoredByUserId = null, ?string $notes = null, ?array $metrics = null)
    {
        return $this->practiceProgressions()->create([
            'patient_id' => $patientId,
            'progress_date' => $date,
            'progress_percentage' => $percentage,
            'status' => $status,
            'monitored_by' => $monitoredBy,
            'monitored_by_user_id' => $monitoredByUserId,
            'notes' => $notes,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get average progression percentage.
     */
    public function averageProgression()
    {
        return $this->practiceProgressions()->avg('progress_percentage');
    }

    /**
     * Get latest progression.
     */
    public function latestProgression()
    {
        return $this->practiceProgressions()->latest('progress_date')->first();
    }

    /**
     * Get progression trend (last 7 days).
     */
    public function progressionTrend(int $days = 7)
    {
        $startDate = Carbon::now()->subDays($days);
        return $this->practiceProgressions()
            ->where('progress_date', '>=', $startDate)
            ->orderBy('progress_date')
            ->get();
    }
}
