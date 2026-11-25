<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class General extends Model
{
    protected $table = 'generales';

    protected $primaryKey = 'idgeneral';

    protected $fillable = [
        'namearea', 
        'fundamentolegal', 
    ];
}
