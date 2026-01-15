<?php

namespace App\Models\FinancialRatios;

use Illuminate\Database\Eloquent\Model;

class FinancialAccounts extends Model
{
    protected $connection = 'sqlsrv'; //Usa la conexión SQL Server SAP
   protected $table ='OACT';
   protected $primaryKey = "AcctCode";
   public $timestamps = false;
}
