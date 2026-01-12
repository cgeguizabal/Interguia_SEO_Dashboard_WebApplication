<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'OBTN';          // SAP Batch Master table
    protected $primaryKey = 'DistNumber'; // SAP primary key
    public $timestamps = false; // SAP no crea created_at / updated_at
}
