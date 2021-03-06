<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyNotice extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status'];
}
