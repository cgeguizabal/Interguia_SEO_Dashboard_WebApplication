<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $connection = 'sqlsrv'; //Usa la conexión SQL Server SAP
   protected $table ='OITB';
   protected $primaryKey = "ItmsGrpCod";
   public $timestamps = false;
}
