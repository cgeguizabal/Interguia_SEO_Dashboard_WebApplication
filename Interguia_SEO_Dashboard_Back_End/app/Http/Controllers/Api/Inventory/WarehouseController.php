<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use Illuminate\Database\QueryException;


class WarehouseController extends Controller
{
    public function index()
    {
        try{
            $warehouse = Warehouse::select('WhsCode', 'WhsName')->get();

            return response()->json([
                'status' => true,
                'data' => $warehouse
            ], 200);
        }catch(QueryException $e){
            return response()->json([
                'status' => false,
                'error' => 'Database query failed',
                'message' => $e->getMessage()
            ], 409);
    }catch(\Exception $e){
            return response()->json([
                'status'=>false,
                'error'=>'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
