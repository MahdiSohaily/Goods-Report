<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Similar extends Model
{
    use HasFactory;
    protected $table = 'similars';
    public $timestamps = false;
}
