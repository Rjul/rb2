<?php

namespace App\Models\OldModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Commentable;

class Emision extends Model
{
    use Commentable;
    //protected $primaryKey = "idEmision";

    public $connection = 'old_db';
}
