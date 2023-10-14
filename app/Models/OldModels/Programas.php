<?php

namespace App\Models\OldModels;

use Illuminate\Database\Eloquent\Model;

class Programas extends Model
{

    public $connection = 'old_db';
    protected $primaryKey = "idPrograma";
}
