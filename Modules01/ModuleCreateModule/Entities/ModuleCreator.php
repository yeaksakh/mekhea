<?php

namespace Modules\ModuleCreateModule\Entities;

use Illuminate\Database\Eloquent\Model;

class ModuleCreator extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'modulecreator'; // Specify the table name
}