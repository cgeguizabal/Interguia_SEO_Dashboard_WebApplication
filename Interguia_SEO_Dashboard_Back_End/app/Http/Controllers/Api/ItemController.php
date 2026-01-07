<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;


use App\Models\Item;
use Illuminate\Http\Request;


class ItemController extends Controller
{
     
  public function index()
{
    try {
     $items = Item::query()
    ->leftJoin('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode') // Une tablas con mismo ID
    ->leftJoin('UGP1', 'OITM.UgpEntry', '=', 'UGP1.UgpEntry') // Une tablas con mismo Unit of Measure Group Entry
    ->leftJoin('OUOM', 'UGP1.UomEntry', '=', 'OUOM.UomEntry') // Une tablas con mismo UomEntry, PK de OUOM
    ->select(
        'OITM.ItemCode as ItemCodeID',
        'OITM.ItemName',
        'OITM.OnHand as TotalStock',
        'OITM.IsCommited',
        'OITM.OnOrder',
        'OITM.InvntryUom as InventoryUnit',
        'OBTN.CreateDate',
        'OBTN.ExpDate',
        'OBTN.AbsEntry as NoRecord',
        'OBTN.DistNumber as BatchNumber',
        'OBTN.Quantity as BatchStock', 
        'OUOM.UomName as UnitName',
        'UGP1.AltQty',
        'UGP1.BaseQty'
    )
    ->get()
    ->groupBy('ItemCodeID')   // group rows por item
    ->map(function($itemGroup) {
        $first = $itemGroup->first();
        return [
            'ItemCode' => $first->ItemCodeID,
            'ItemName' => $first->ItemName,
            'TotalStock' => $first->TotalStock,
            'InventoryUnit' => $first->InventoryUnit,
            'Batches' => $itemGroup->unique('BatchNumber')->values()->map(fn($b) => [
                'BatchNumber' => $b->BatchNumber,
                'CreateDate' => $b->CreateDate,
                'ExpDate' => $b->ExpDate,
                'NoRecord' => $b->NoRecord,
                'StockInBatch' => $b->BatchStock, 
            ]),
            'Conversions' => $itemGroup->unique('UnitName')->map(fn($c) => [
                'UnitName' => $c->UnitName,
                'UnitAmount' => $c->AltQty,
                'BaseQty' => $c->BaseQty,
                'StockInUnit' => $first->TotalStock * ($c->AltQty / $c->BaseQty)
            ])
        ];
    })
    ->values();


        return response()->json([
            'status' => true,
            'data' => $items
        ], 200);
        
    } catch (QueryException $e) {
        
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

public function itemByBatch($itemCode)
{
    try {
       
        $items = Item::query()
            ->leftJoin('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode') // join batch table
            ->leftJoin('UGP1', 'OITM.UgpEntry', '=', 'UGP1.UgpEntry') // join unit group table
            ->leftJoin('OUOM', 'UGP1.UomEntry', '=', 'OUOM.UomEntry') // join unit table
            ->where('OBTN.DistNumber', $itemCode) // filter by batch number
            ->select(
                'OITM.ItemCode as ItemCodeID',
                'OITM.ItemName',
                'OITM.OnHand as TotalStock',
                'OITM.IsCommited',
                'OITM.OnOrder',
                'OITM.InvntryUom as InventoryUnit',
                'OBTN.CreateDate',
                'OBTN.ExpDate',
                'OBTN.AbsEntry as NoRecord',
                'OBTN.DistNumber as BatchNumber',
                'OBTN.Quantity as BatchStock', 
                'OUOM.UomName as UnitName',
                'UGP1.AltQty',
                'UGP1.BaseQty'
            )
            ->get()
            ->groupBy('ItemCodeID') // group by item
            ->map(function($itemGroup) {
                $first = $itemGroup->first();
                return [
                    'ItemCode' => $first->ItemCodeID,
                    'ItemName' => $first->ItemName,
                    'TotalStock' => $first->TotalStock,
                    'InventoryUnit' => $first->InventoryUnit,
                    'Batches' => $itemGroup->unique('BatchNumber')->values()->map(fn($b) => [
                        'BatchNumber' => $b->BatchNumber,
                        'CreateDate' => $b->CreateDate,
                        'ExpDate' => $b->ExpDate,
                        'NoRecord' => $b->NoRecord,
                        'StockInBatch' => $b->BatchStock, 
                    ]),
                    'Conversions' => $itemGroup->unique('UnitName')->map(fn($c) => [
                        'UnitName' => $c->UnitName,
                        'UnitAmount' => $c->AltQty, // alternative quantity
                        'BaseQty' => $c->BaseQty,
                        'StockInUnit' => $first->TotalStock * ($c->AltQty / $c->BaseQty)
                    ])
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $items
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}

 public function itemBySerie($itemCode)
{
    try {
        $items = Item::query()
    ->leftJoin('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode') // batches
    ->leftJoin('UGP1', 'OITM.UgpEntry', '=', 'UGP1.UgpEntry') // unit groups
    ->leftJoin('OUOM', 'UGP1.UomEntry', '=', 'OUOM.UomEntry') // units
    ->leftJoin('OITW', 'OITM.ItemCode', '=', 'OITW.ItemCode') // warehouse stock
    ->where('OITM.ItemCode', $itemCode)
    ->select(
        'OITM.ItemCode as ItemCodeID',
        'OITM.ItemName',
        'OITM.OnHand as TotalStock',
        'OITM.IsCommited',
        'OITM.OnOrder',
        'OITM.InvntryUom as InventoryUnit',

        'OITW.WhsCode',
        'OITW.OnHand as WarehouseStock',

        'OBTN.CreateDate',
        'OBTN.ExpDate',
        'OBTN.AbsEntry as NoRecord',
        'OBTN.DistNumber as BatchNumber',
        'OBTN.Quantity as BatchStock',

        'OUOM.UomName as UnitName',
        'UGP1.AltQty',
        'UGP1.BaseQty'
    )
    ->get()
    ->groupBy('ItemCodeID')
    ->map(function ($itemGroup) {
        $first = $itemGroup->first();

        return [
            'ItemCode' => $first->ItemCodeID,
            'ItemName' => $first->ItemName,
            'TotalStock' => $first->TotalStock,
            'InventoryUnit' => $first->InventoryUnit,

            'Warehouses' => $itemGroup
    ->where('WarehouseStock', '>', 0)
    ->unique('WhsCode')
    ->values()
    ->map(fn ($w) => [
        'WhsCode' => $w->WhsCode,
        'Stock' => $w->WarehouseStock,
    ]),


            'Batches' => $itemGroup
                ->unique('BatchNumber')
                ->values()
                ->map(fn ($b) => [
                    'BatchNumber' => $b->BatchNumber ?? null,
                    'CreateDate' => $b->CreateDate ?? null,
                    'ExpDate' => $b->ExpDate ?? null,
                    'NoRecord' => $b->NoRecord ?? null,
                    'StockInBatch' => $b->BatchStock ?? 0,
                ]),

            'Conversions' => $itemGroup
                ->unique('UnitName')
                ->values()
                ->map(fn ($c) => [
                    'UnitName' => $c->UnitName,
                    'UnitAmount' => $c->AltQty,
                    'BaseQty' => $c->BaseQty,
                    'StockInUnit' => $first->TotalStock * ($c->AltQty / $c->BaseQty),
                ]),
        ];
    })
    ->values();


        return response()->json([
            'status' => true,
            'data' => $items
        ], 200);

    } catch (QueryException $e) {
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

public function itemByCategory($itemcCode){

    

}

}








