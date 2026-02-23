<?php

namespace Modules\KPI\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'title', 'department_id', 'designation_id'];

    /**
     * Relationship: Indicator has many competencies.
     */
    public function competencies()
    {
        return $this->hasMany(KpiCompetency::class, 'indicator_id');  // explicitly state the foreign key
    }



    /**
     * Relationship: Indicator has many appraisals.
     */
    public function appraisals()
    {
        return $this->hasMany(KpiAppraisal::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Category::class, 'department_id');
    }
}
