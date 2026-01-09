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
      $items = Item::query() // La tabla OITM es mi base
    ->leftJoin('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode') // batches / lotes
    ->leftJoin('OUGP', 'OITM.UgpEntry', '=', 'OUGP.UgpEntry') // grupos de unidades
    ->leftJoin('UGP1', 'OUGP.UgpEntry', '=', 'UGP1.UgpEntry') // conversiones de unidades
    ->leftJoin('OUOM as AltUOM', 'UGP1.UomEntry', '=', 'AltUOM.UomEntry') // unidad alternativa
    ->leftJoin('OUOM as BaseUOM', 'OUGP.BaseUom', '=', 'BaseUOM.UomEntry') // unidad base
    ->leftJoin('OITW', 'OITM.ItemCode', '=', 'OITW.ItemCode') // stock por almacén
    ->leftJoin('OWHS', 'OWHS.WhsCode', '=', 'OITW.WhsCode') // Tabla donde estan los nombres de Almacenes
    ->where('OITM.ItemCode', $itemCode) // Busca el Articulo segun ID Other/Non-inventory
    ->select(

        //Datos de tabla base de inventario
        'OITM.ItemCode as ItemCodeID', // ID/Numero de serie de Articulo
        'OITM.ItemName', // Nombre de Item/Articulo
        'OITM.OnHand as TotalStock', // Total de articulo en unidad base dentro inventario
        'OITM.InvntryUom as InventoryUnit', // Nombre del grupo de unidad
        'OITM.ItemType as ItemType', //Tipo de articulo, tenemos Servicios, productos fisicos y Other/Non-inventory

        //Almacen
        'OITW.WhsCode', // ID de Almancen
        'OWHS.WhsName as WarehouseName', //Nombre del Almacen
        'OITW.OnHand as WarehouseStock', // Total de articulo en unidad base dentro de este almacen
        
        //LOTES
        'OBTN.CreateDate', // Fecha en que se creo
        'OBTN.ExpDate', // Fecha en que expira
        'OBTN.AbsEntry as NoRecord', // Numero de registro
        'OBTN.DistNumber as BatchNumber', // Numero de lote
        'OBTN.Quantity as BatchStock', //Cantidad de ese producto en lote

        //Conversiones
        'AltUOM.UomName as AltUnitName', //Nombre de unidad
        'UGP1.AltQty', // Cantidad base de unidad, siempre es 1
        'UGP1.BaseQty', // Equivalencia de la unidad en la unidad base, por ejemplo, 1 Tonelada = 2204.620000lb
        'BaseUOM.UomName as BaseUnitName' // Nombre de unidad base, ejemplo Libras
    )
    ->get() //Funcion que ejecuta el query
    ->groupBy('ItemCodeID') // Agrupa por ID
    ->map(function ($itemGroup) { // map corre loop para ir grupo por grupo

        //First es la primera fila de todo el grupo que se formo
        $first = $itemGroup->first();

        // Conversión de la unidad de inventario (ej: SACO) a la unidad base
        $inventoryConversion = $itemGroup
            ->firstWhere('AltUnitName', $first->InventoryUnit);

        $inventoryBaseQty = ($inventoryConversion->BaseQty ?? 1); // Obtengo la conversion de la unidad base a la unidad de este grupo

        $totalStock = $first->TotalStock; // El total de articulos en la unidad de inventario/grupo, ejemplo saco

        $itemType = function() use($first){ // Funcion que define el tipo de inventario
            if($first->ItemType = 'I'){ 
                return 'Articulo';
            } else if($first->ItemType = 'S'){
                return 'Servicio';
            } else{
                return 'Otros';
            };
        };

       

        return [
            //ARTICULO CON UNIDAD PRINCIPAL
            'ItemCode' => $first->ItemCodeID, //Numero de serie o ID
            'ItemName' => $first->ItemName, // Nombre del articulo
            'TotalStock' => $first->TotalStock, // Total de inventario en la unidad principal en la que fue guardada
            'InventoryUnit' => $first->InventoryUnit, // Unidad en la que fue gurdada
            'ItemType' => $itemType(), // Tipo de articulo

            //ALMACENES
            'Warehouses' => $itemGroup
                ->where('WarehouseStock', '>', 0) // Si no hay articulos en ese inventario no obtendre informacion de ese almacen
                ->unique('WhsCode') // El numero de lote siempre sera unico
                ->values() // Transforma el array asociativo a array indexado
                ->map(fn ($w) => [
                    'WhsCode' => $w->WhsCode,
                    'Warehouse' =>$w->WarehouseName,
                    'Stock' => $w->WarehouseStock,

                ]),

            //LOTES
            'Batches' => $itemGroup
                ->unique('BatchNumber') // El numero de lote siempre sera unico
                ->values() // Transforma el array asociativo a array indexado
                ->map(fn ($b) => [
                    'BatchNumber' => $b->BatchNumber ?? null,
                    'CreateDate' => $b->CreateDate ?? null,
                    'ExpDate' => $b->ExpDate ?? null,
                    'NoRecord' => $b->NoRecord ?? null,
                    'StockInBatch' => $b->BatchStock ?? 0,
                ]),


            //CONVERSIONES
            'Conversions' => $itemGroup
                ->unique('AltUnitName')
                ->values() // Transforma el array asociativo a array indexado
                ->map(function ($c) use ($inventoryBaseQty, $totalStock) {

                    return [
                        'UnitName' => $c->AltUnitName, // Unidad de conversion (Ej.Toneladas)
                        'UnitAmount' => $c->AltQty, // Cantidad de unidad de conversion
                        'BaseUnitName' => $c->BaseUnitName, // Unidad base (Ej.Libras)
                        'BaseQty' => $c->BaseQty, // Valor de Unidad de conversion a unidad base (Ej.valor de toneladas a libras)

                        // TotalStock * (Valor de unidad(Ej.Toneladas) en la unidad mas pequena(Ej. Libras)) / (Total de unidad a convertir(Ej.Toneladas) en unidad mas pequena(Ej. Libras)
                        'StockInUnit' => $totalStock * $inventoryBaseQty / $c->BaseQty,

                    ];
                }),
        ];
    })
    ->values(); // Transforma el array asociativo a array indexado



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

    try{


        $items = Item::query()->leftJoin('OITB', 'OITM.ItmsGrpCod', '=', 'OITB.ItmsGrpCod')
        ->where('OITM.ItmsGrpCod', $itemcCode)
        ->select('OITM.ItemCode as ItemCodeID',
        'OITM.ItemName',)->get();

        /* $items = Item::query()
            ->where('ItmsGrpCod', $itemcCode) // filter by category code
            ->select('ItemCode as ItemCodeID', 'ItemName')
            ->get(); */

        return response()->json([
            'status' => true,
            'data' => $items
        ],200);


    }catch (QueryException $e) {
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage()
        ], 409);

    }catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }

}

//PEDIENTE FUNCION PUBLICA PARA OBTENER INVENTARIO POR ID DE CLIENTE


}








