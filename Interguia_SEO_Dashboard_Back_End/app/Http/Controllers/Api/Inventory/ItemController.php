<?php

namespace App\Http\Controllers\Api\Inventory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;


use App\Models\Inventory\Item;
use Illuminate\Http\Request;


class ItemController extends Controller
{ 

    //METODO para obtener todos los articulos/items
    public function index(Request $request) //Dato enviando en URL http://127.0.0.1:8000/api/v1/items?page=50
{
    try {

        //Obtengo numero de pagina de URL http://127.0.0.1:8000/api/v1/items?page=50
        $page = (int) $request->query('page', 1);


        //Se tuvo que agrega paginacion porque la memoria se agotaba por la cantidad gigante de datos
        $perPage = 1000; // Items/Articulos por pagiona
        $skip = ($page - 1) * $perPage; //Salta esta cantidad de articulos de los datos obtenidos
        
     $items = Item::query()
    ->leftJoin('OBTN', 'OITM.ItemCode', '=', 'OBTN.ItemCode') // batches / lotes
    ->leftJoin('OUGP', 'OITM.UgpEntry', '=', 'OUGP.UgpEntry') // grupos de unidades
    ->leftJoin('UGP1', 'OITM.UgpEntry', '=', 'UGP1.UgpEntry') // conversiones de unidades
    ->leftJoin('OUOM as AltUOM', 'UGP1.UomEntry', '=', 'AltUOM.UomEntry') // unidad alternativa
    ->leftJoin('OUOM as BaseUOM', 'OUGP.BaseUom', '=', 'BaseUOM.UomEntry') // unidad base
    ->leftJoin('OITW', 'OITM.ItemCode', '=', 'OITW.ItemCode') // stock por almacén
    ->leftJoin('OWHS', 'OWHS.WhsCode', '=', 'OITW.WhsCode') // Tabla donde estan los nombres de Almacenes
    ->select(

        //Datos de tabla base de inventario
       'OITM.ItemCode as ItemCodeID', // ID/Numero de serie de Articulo
        'OITM.ItemName', // Nombre de Item/Articulo
        'OITM.OnHand as TotalStock', // Total de articulo en unidad base dentro inventario
        'OITM.InvntryUom as InventoryUnit', // Nombre del grupo de unidad
        'OITM.ItemType as ItemType', //Tipo de articulo, tenemos Servicios, productos fisicos y Other/Non-inventory
        'OITM.IsCommited as Commited', // Articulos reservados para ordenes
        'OITM.OnOrder as Ordered', // Inventario en camino de provedores 


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
    ->skip($skip) // Saltar x cantidad de datos
    ->take($perPage) // Tomar x cantidad de datos
    ->get()
    ->filter(fn ($r) => $r->ItemCodeID !== null)
    ->groupBy('ItemCodeID')   // Agrupa por ID
    ->map(function($itemGroup) { // map corre loop para ir grupo por grupo

        //First es la primera fila de todo el grupo que se formo
        $first = $itemGroup->first();

        // Conversión de la unidad de inventario (ej: SACO) a la unidad base
        $inventoryConversion = null;
        if (!empty($first->InventoryUnit)) {
            $inventoryConversion = $itemGroup->firstWhere('AltUnitName', $first->InventoryUnit);
        }

        // Obtengo la conversion de la unidad base a la unidad de este grupo
        $inventoryBaseQty = ($inventoryConversion && $inventoryConversion->BaseQty) ? $inventoryConversion->BaseQty : 1;

        // El total de articulos en la unidad de inventario/grupo, ejemplo saco
        $totalStock = $first->TotalStock;

        // Funcion que define el tipo de inventario
        $itemType = function() use($first){ 
            if($first->ItemType == 'I'){ 
                return 'Articulo';
            } else if($first->ItemType == 'S'){
                return 'Servicio';
            } else{
                return 'Otros';
            };
        };

        //Disponibilidad
        $availability = $first->TotalStock - $first->Commited + $first->Ordered;

        return [
             //ARTICULO CON UNIDAD PRINCIPAL
            'ItemCode' => $first->ItemCodeID, //Numero de serie o ID
            'ItemName' => $first->ItemName, // Nombre del articulo
            'TotalStock' => $first->TotalStock, // Total de inventario en la unidad principal en la que fue guardada
            'InventoryUnit' => $first->InventoryUnit, // Unidad en la que fue gurdada
            'ItemType' => $itemType(), // Tipo de articulo
            'Commited' => $first->Commited,
            'Ordered' => $first->Ordered,
            "Available" => $availability,

            //ALMACENES
            'Warehouses' => $itemGroup
                ->where('WarehouseStock', '>', 0) // Si no hay articulos en ese inventario no obtendre informacion de ese almacen
                ->unique('WhsCode') // El codigo de almacen siempre sera unico
                ->values() // Transforma el array asociativo a array indexado
                ->map(fn ($w) => [
                    'WhsCode' => $w->WhsCode,
                    'Warehouse' => $w->WarehouseName,
                    'Stock' => $w->WarehouseStock,
                ]),

            //LOTES
            'Batches' => $itemGroup
            ->unique('BatchNumber') // El numero de lote siempre sera unico
            ->values() // Transforma el array asociativo a array indexado
            ->map(fn($b) => [
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
            ->map(function ($c) use($inventoryBaseQty, $totalStock) {

                return [
                 'UnitName' => $c->AltUnitName, // Unidad de conversion (Ej.Toneladas)
                'UnitAmount' => $c->AltQty, // Cantidad de unidad de conversion
                'BaseUnitName' => $c->BaseUnitName, // Unidad base (Ej.Libras)
                'BaseQty' => $c->BaseQty,// Valor de Unidad de conversion a unidad base (Ej.valor de toneladas a libras)
            
                // TotalStock * (Valor de unidad(Ej.Toneladas) en la unidad mas pequena(Ej. Libras)) / (Total de unidad a convertir(Ej.Toneladas) en unidad mas pequena(Ej. Libras)
                'StockInUnit' => $totalStock * $inventoryBaseQty / $c->BaseQty

                ];
            }),
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

//METODO para obtener artculos por numero de LOTE

public function itemByBatch($itemCode)
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
    ->where('OBTN.DistNumber', $itemCode) // filtra por batch.lote number
    ->select(

        //Datos de tabla base de inventario
        'OITM.ItemCode as ItemCodeID', // ID/Numero de serie de Articulo
        'OITM.ItemName', // Nombre de Item/Articulo
        'OITM.OnHand as TotalStock', // Total de articulo en unidad base dentro inventario
        'OITM.InvntryUom as InventoryUnit', // Nombre del grupo de unidad
        'OITM.ItemType as ItemType', //Tipo de articulo, tenemos Servicios, productos fisicos y Other/Non-inventory
        'OITM.IsCommited as Commited', // Articulos reservados para ordenes
        'OITM.OnOrder as Ordered', // Inventario en camino de provedores 

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


        // Obtengo la conversion de la unidad base a la unidad de este grupo
        $inventoryBaseQty = ($inventoryConversion->BaseQty ?? 1); 


        // El total de articulos en la unidad de inventario/grupo, ejemplo saco
        $totalStock = $first->TotalStock; 


        // Funcion que define el tipo de inventario
        $itemType = function() use($first){ 
            if($first->ItemType == 'I'){ 
                return 'Articulo';
            } else if($first->ItemType == 'S'){
                return 'Servicio';
            } else{
                return 'Otros';
            };
        };

        //Disponibilidad
        $availability = $first->TotalStock - $first->Commited + $first->Ordered;


        return [
            //ARTICULO CON UNIDAD PRINCIPAL
            'ItemCode' => $first->ItemCodeID, //Numero de serie o ID
            'ItemName' => $first->ItemName, // Nombre del articulo
            'TotalStock' => $first->TotalStock, // Total de inventario en la unidad principal en la que fue guardada
            'InventoryUnit' => $first->InventoryUnit, // Unidad en la que fue gurdada
            'ItemType' => $itemType(), // Tipo de articulo
            'Commited' => $first->Commited,
            'Ordered' => $first->Ordered,
            "Available" => $availability, //disponibilidad

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
        ], 200); // Si obtenemos todos los datos con exito regresa el json completo con datos y estado

    } catch (QueryException $e) {
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage() // Mensaje especifico de error que viene de la base SQL
        ], 409);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage() // Mensaje de error general
        ], 500);
    }
}


//METODO obtener articulos por numero de serie/ID
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
        'OITM.IsCommited as Commited', // Articulos reservados para ordenes
        'OITM.OnOrder as Ordered', // Inventario en camino de provedores 

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


        // Obtengo la conversion de la unidad base a la unidad de este grupo
        $inventoryBaseQty = ($inventoryConversion->BaseQty ?? 1); 


        // El total de articulos en la unidad de inventario/grupo, ejemplo saco
        $totalStock = $first->TotalStock; 


        // Funcion que define el tipo de inventario
        $itemType = function() use($first){ 
            if($first->ItemType == 'I'){ 
                return 'Articulo';
            } else if($first->ItemType == 'S'){
                return 'Servicio';
            } else{
                return 'Otros';
            };
        };

        //Disponibilidad
        $availability = $first->TotalStock - $first->Commited + $first->Ordered;


        return [
            //ARTICULO CON UNIDAD PRINCIPAL
            'ItemCode' => $first->ItemCodeID, //Numero de serie o ID
            'ItemName' => $first->ItemName, // Nombre del articulo
            'TotalStock' => $first->TotalStock, // Total de inventario en la unidad principal en la que fue guardada
            'InventoryUnit' => $first->InventoryUnit, // Unidad en la que fue gurdada
            'ItemType' => $itemType(), // Tipo de articulo
            'Commited' => $first->Commited,
            'Ordered' => $first->Ordered,
            "Available" => $availability,

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
        ], 200); // Si obtenemos todos los datos con exito regresa el json completo con datos y estado

    } catch (QueryException $e) {
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage() // Mensaje especifico de error que viene de la base SQL
        ], 409);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage() // Mensaje de error general
        ], 500);
    }
}



//METODO obtener Articulos por categoria
public function itemByCategory($itemcCode)
{
    try{


        $items = Item::query()->leftJoin('OITB', 'OITM.ItmsGrpCod', '=', 'OITB.ItmsGrpCod') // tabla de categorias
        ->where('OITM.ItmsGrpCod', $itemcCode) // filtrar por category code
        ->select('OITM.ItemCode as ItemCodeID',
        'OITM.ItemName',)->get();


        //LOGICA OPCIONAL PARA OBTENER DATOS
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


//METODO Obtener almacenes
public function itemByWarehouse($whsCode)
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
    ->where('OITW.WhsCode', $whsCode) // filtra numero de almacen
    ->whereNotNull('OITW.OnHand') // Si dice null no se regresaran datos de ese item dentro del almacen, porque no existe
    ->select(

        //Datos de tabla base de inventario
        'OITM.ItemCode as ItemCodeID', // ID/Numero de serie de Articulo
        'OITM.ItemName', // Nombre de Item/Articulo
        'OITM.OnHand as TotalStock', // Total de articulo en unidad base dentro inventario
        'OITM.InvntryUom as InventoryUnit', // Nombre del grupo de unidad
        'OITM.ItemType as ItemType', //Tipo de articulo, tenemos Servicios, productos fisicos y Other/Non-inventory
        'OITM.IsCommited as Commited', // Articulos reservados para ordenes
        'OITM.OnOrder as Ordered', // Inventario en camino de provedores 

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
    ->map(function ($itemGroup) use ($whsCode) { // map corre loop para ir grupo por grupo

        //First es la primera fila de todo el grupo que se formo
        $first = $itemGroup->first();


        // Conversión de la unidad de inventario (ej: SACO) a la unidad base
        $inventoryConversion = $itemGroup
            ->firstWhere('AltUnitName', $first->InventoryUnit);


        // Obtengo la conversion de la unidad base a la unidad de este grupo
        $inventoryBaseQty = ($inventoryConversion->BaseQty ?? 1); 


        // El total de articulos en la unidad de inventario/grupo, ejemplo saco
        $totalStock = $first->TotalStock; 


        // Funcion que define el tipo de inventario
        $itemType = function() use($first){ 
            if($first->ItemType == 'I'){ 
                return 'Articulo';
            } else if($first->ItemType == 'S'){
                return 'Servicio';
            } else{
                return 'Otros';
            };
        };

        //Disponibilidad
        $availability = $first->TotalStock - $first->Commited + $first->Ordered;


        return [
            //ARTICULO CON UNIDAD PRINCIPAL
            'ItemCode' => $first->ItemCodeID, //Numero de serie o ID
            'ItemName' => $first->ItemName, // Nombre del articulo
            'TotalStock' => $first->TotalStock, // Total de inventario en la unidad principal en la que fue guardada
            'InventoryUnit' => $first->InventoryUnit, // Unidad en la que fue gurdada
            'ItemType' => $itemType(), // Tipo de articulo
            'Commited' => $first->Commited,
            'Ordered' => $first->Ordered,
            "Available" => $availability,

            //ALMACENES
            'Warehouses' => $itemGroup
                ->where('WhsCode', $whsCode) // Solo incluye items de almacen solicitado
                ->unique('WhsCode') // El numero de lote siempre sera unico
                ->values() // Transforma el array asociativo a array indexado
                ->map(fn ($w) => [
                    'WhsCode' => $w->WhsCode,
                    'Warehouse' =>$w->WarehouseName,
                    'Stock' => $w->WarehouseStock,

                ])
                ->filter(fn ($w) => (float)$w['Stock'] > 0) // Filter out warehouses with 0 stock
                ->values(),

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
    ->filter(function($item) {
        return count($item['Warehouses']) > 0; // Si no hay items/articulos en este almacen, no va a regresar ningun dato
    })
    ->values(); // Transforma el array asociativo a array indexado



        return response()->json([
            'status' => true,
            'data' => $items
        ], 200); // Si obtenemos todos los datos con exito regresa el json completo con datos y estado

    } catch (QueryException $e) {
        return response()->json([
            'status' => false,
            'error' => 'Database query failed',
            'message' => $e->getMessage() // Mensaje especifico de error que viene de la base SQL
        ], 409);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Something went wrong',
            'message' => $e->getMessage() // Mensaje de error general
        ], 500);
    }
}

//PEDIENTE FUNCION PUBLICA PARA OBTENER INVENTARIO POR ID DE CLIENTE


}









