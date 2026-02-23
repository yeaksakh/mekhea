<?php

namespace Modules\KPI\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAppraisal extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'indicator_id',
        'employee_id',
        'business_id',
        'appraisal_month',
        'created_by', // This stores the ID of the creator
    ];

    /**
     * Relationship: Appraisal belongs to an indicator.
     */
    public function indicator()
    {
        return $this->belongsTo(KpiIndicator::class);
    }

    /**
     * Relationship: Appraisal belongs to an employee (User model).
     */
    public function employee()
    {
        return $this->belongsTo(\App\User::class, 'employee_id');
    }

    /**
     * Relationship: Appraisal has many appraisal scores.
     */
    public function scores()
    {
        return $this->hasMany(KpiAppraisalScore::class , 'appraisal_id');
    }

    /**
     * Relationship: Appraisal was created by a user.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
