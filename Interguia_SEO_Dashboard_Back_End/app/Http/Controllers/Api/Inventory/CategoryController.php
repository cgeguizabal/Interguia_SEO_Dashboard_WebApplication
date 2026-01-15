<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function index(){

        try{
            $categories = Category::select('ItmsGrpCod', 'ItmsGrpNam')->get();

            return response()->json([
                'status' => true,
                'data' => $categories
            ], 200);
        } catch(QueryException $e){

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
