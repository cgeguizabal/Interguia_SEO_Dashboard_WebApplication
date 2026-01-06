<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'OITM';          // SAP Item Master table
    protected $primaryKey = 'ItemCode'; // SAP primary key
    public $timestamps = false; // SAP has no created_at / updated_at
}
