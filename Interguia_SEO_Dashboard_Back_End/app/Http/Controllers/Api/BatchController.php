<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Database\QueryException;


class BatchController extends Controller
{
    public function index(){
       
        try{
             $batchNumbers = Batch::select('DistNumber')->get(); // Obtiene todos los números de lote

             $data = $batchNumbers->map(function($batch){
                return [
                    'BatchNumber' => $batch->DistNumber // Accede al campo DistNumber de la tabla OBTN
                ];
             });

return response()->json([
                'status' => true,
                'data' => $data
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
