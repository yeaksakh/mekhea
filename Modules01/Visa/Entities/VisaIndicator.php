<?php

namespace Modules\Visa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'title', 'department_id', 'designation_id'];

    /**
     * Relationship: Indicator has many competencies.
     */
    public function competencies()
    {
        return $this->hasMany(VisaCompetency::class, 'indicator_id');  // explicitly state the foreign key
    }



    /**
     * Relationship: Indicator has many appraisals.
     */
    public function appraisals()
    {
        return $this->hasMany(VisaAppraisal::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Category::class, 'department_id');
    }
}
