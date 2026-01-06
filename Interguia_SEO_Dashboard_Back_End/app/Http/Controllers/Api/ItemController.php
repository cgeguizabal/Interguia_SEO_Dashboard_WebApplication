<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;


use App\Models\Item;

class ItemController extends Controller
{
     
  public function index()
{
    try {
        $items = Item::query()
            ->join('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode')
            ->select(
                'OITM.ItemCode as ItemCodeID',
                'OITM.ItemName',
                'OITM.OnHand as stock',
                'OITM.IsCommited',
        'OITM.OnOrder',
                'OITM.InvntryUom as Unit',
                'OBTN.CreateDate',
                'OBTN.ExpDate',
                'OBTN.AbsEntry as NoRecord',
                'OBTN.DistNumber as BatchNumber'              
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $items
        ], 200);
        
    } catch (QueryException $e) {
        // This catches SQL / DB errors
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage()
        ], 409);
        
    } catch (\Exception $e) {
        // This catches all other PHP errors
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}
}








