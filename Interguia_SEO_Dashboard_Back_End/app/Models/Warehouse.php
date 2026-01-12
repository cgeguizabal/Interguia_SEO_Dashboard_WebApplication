<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $connection = 'sqlsrv'; //Usa la conexión SQL Server SAP
    protected $table = 'OWHS';          // SAP Warehouse Master table
    protected $primaryKey = 'WhsCode'; // SAP primary key
    public $timestamps = false; // SAP no crea created_at / updated_at
}
