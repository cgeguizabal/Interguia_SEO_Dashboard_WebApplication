<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
        protected $connection = 'sqlsrv';   //Usa la conexión SQL Server SAP
    protected $table = 'OITM';          // SAP Item Master table
    protected $primaryKey = 'ItemCode'; // SAP primary key
    public $timestamps = false; // SAP no crea created_at / updated_at
}
