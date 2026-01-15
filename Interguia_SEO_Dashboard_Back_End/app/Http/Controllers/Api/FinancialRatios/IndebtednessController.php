<?php

namespace App\Http\Controllers\Api\FinancialRatios;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialRatios\FinancialAccounts as Account;


class IndebtednessController extends Controller
{
   

   public function percentageOfFinancedAssets($date)
{

    // OACT tabla de cuentas contables
    // JDT1 tabla de detalles de diario 
    // OJDT tabla de encabezados de diario, significa Journal Entries, meta data de asientos contables
    try {
        // Total de Activos/Assets
        $totalAssetsQuery = DB::connection('sqlsrv')
            ->table('JDT1')
            ->join('OJDT', 'JDT1.TransId', '=', 'OJDT.TransId') // Une con la tabla de transacciones
            ->join('OACT', 'JDT1.Account', '=', 'OACT.AcctCode') // Une con la tabla de cuentas
            ->where('OJDT.RefDate', '<=', $date) // Filtra hasta la fecha dada
            ->where('OACT.GroupMask', 1) // GroupMask 1 representa activos/assets
            ->selectRaw('SUM(JDT1.Debit - JDT1.Credit) as total_assets')
            ->first();

            // Define totalAssets con un valor predeterminado de 0 si es null
        $totalAssets = $totalAssetsQuery->total_assets ?? 0;

        // Total de Pasivos/Liabilities
        $totalDebtQuery = DB::connection('sqlsrv')
            ->table('JDT1')
            ->join('OJDT', 'JDT1.TransId', '=', 'OJDT.TransId') // Une con la tabla de transacciones
            ->join('OACT', 'JDT1.Account', '=', 'OACT.AcctCode') // Une con la tabla de cuentas
            ->where('OJDT.RefDate', '<=', $date) // Filtra hasta la fecha dada
            ->where('OACT.GroupMask', 2) // GroupMask 2 representa pasivos/liabilities
            ->selectRaw('SUM(JDT1.Credit - JDT1.Debit) as total_debt')
            ->first();

            // Define totalDebt con un valor predeterminado de 0 si es null
        $totalDebt = $totalDebtQuery->total_debt ?? 0;

        // Calcula el porcentaje de activos financiados con deuda
        $percentage = $totalAssets > 0
            ? round(($totalDebt / $totalAssets) * 100, 2) // Porcenaje de activos financiados con deuda = (Total Pasivos / Total Activos) * 100
            : null;

        return response()->json([
            'status' => true,
            'data' => [
                'date' => $date,
                'percentage_of_assets_financed_with_debt' => $percentage
            ]
        ], 200);

    }catch(QueryException $e){
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage()
        ], 409);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}


}
