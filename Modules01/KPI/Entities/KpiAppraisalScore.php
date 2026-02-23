<?php

namespace Modules\KPI\Entities;

use App\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAppraisalScore extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'appraisal_id',
        'competency_id',
        'business_id',
        'expect_value',
        'expect_score',
        'actual_value',
        'actual_score',
        'note', // Add note
    ];


    /**
     * Relationship: AppraisalScore belongs to an appraisal.
     */
    public function appraisal()
    {
        return $this->belongsTo(KpiAppraisal::class, 'appraisal_id');
    }

    public function competency()
    {
        return $this->belongsTo(KpiCompetency::class, 'competency_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
