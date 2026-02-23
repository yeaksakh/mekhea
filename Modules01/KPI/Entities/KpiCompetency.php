<?php

namespace Modules\KPI\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiCompetency extends Model
{
    use HasFactory;

    protected $fillable = ['indicator_id', 'type', 'name', 'value', 'score', 'business_id'];

    /**
     * Relationship: Competency belongs to an indicator.
     */
    public function indicator()
    {
        return $this->belongsTo(KpiIndicator::class, 'indicator_id');
    }

    /**
     * Relationship: KpiCompetency has many appraisal scores.
     */
    public function appraisalScores()
    {
        return $this->hasMany(KpiAppraisalScore::class);
    }
}
