<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStep extends Model
{
    protected $fillable = [
        'user_id',
        'recipe_id',
        'step_number'
    ];
}
