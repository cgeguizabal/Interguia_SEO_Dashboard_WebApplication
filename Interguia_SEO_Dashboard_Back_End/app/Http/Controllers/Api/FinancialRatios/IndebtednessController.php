<?php

namespace App\Http\Controllers\Api\FinancialRatios;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialRatios\FinancialAccounts as Account;



class IndebtednessController extends Controller
{

   //Obtiene la lista de cuentas para pasivos y que se puedan seleccionar en el front-end
   public function liabilityAccounts(){

   try{
        // $accounts = Account::where('GroupMask', 2)->get();

        $accounts = Account::where('GroupMask', 2)->select('AcctCode', 'AcctName', 'GroupMask')->get();

        return response()->json([
            'status' => true,
            'data' => $accounts
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


 

public function getLongTermDebtTotal(Request $request)
{
    try {

        $request->validate([
            'acct_codes' => 'required|array',
            'acct_codes.*' => 'string',
            'ref_date' => 'required|date',
        ]);

        $acctCodes = $request->acct_codes; // array of AcctCode
        $refDate   = $request->ref_date;

        //Journal Entries, detalles de asientos contables
        $result = DB::connection('sqlsrv') 
            ->table('JDT1')
            ->join('OJDT', 'JDT1.TransId', '=', 'OJDT.TransId')
            ->whereIn('JDT1.Account', $acctCodes)
            ->where('OJDT.RefDate', '<=', $refDate)
            ->selectRaw('
                SUM(JDT1.Credit) AS total_credit,
                SUM(JDT1.Debit) AS total_debit
            ')
            ->first();

        //Calcula la deuda a largo plazo
        $longTermDebt = ($result->total_credit ?? 0) - ($result->total_debit ?? 0);

        
        return response()->json([
            'ref_date' => $refDate,
            'accounts' => $acctCodes,
            'long_term_debt_total' => $longTermDebt
        ]);

    } catch(QueryException $e){
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



    // Calcula el porcentaje de activos financiados con deuda hasta una fecha dada
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
                'UpToDate' => $date, // Fecha hasta la cual se calcula
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
