<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\SeoDatabaseHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class SeoDatabaseController extends Controller
{
    public function setup(Request $request)
    {
        try {
            // Validate input
            $data = $request->validate([
                'host' => 'required|string',
                'port' => 'required|integer',
                'username' => 'required|string',
                'password' => 'required|string',
                'database' => 'required|string',
            ]);

            // Define conexion y guarda credentials
            $success = SeoDatabaseHelper::setSeoConnections(
                $data['host'],
                $data['port'],
                $data['username'],
                $data['password'],
                $data['database']
            );

            if (!$success) {
                return response()->json(['error' => 'Failed to set database connections'], 500);
            }

            // Check si existe la base de datos
            $exists = DB::connection('sqlsrv_master')
                ->select("SELECT name FROM sys.databases WHERE name = ?", [$data['database']]);

            if (!empty($exists)) {
                return response()->json(['message' => "Database '{$data['database']}' already exists."]);
            }

            // Crea database
            DB::connection('sqlsrv_master')->statement("CREATE DATABASE [{$data['database']}]");


            //Crea migraciones iniciales, mis tablas de usuarios, users_roles, roles, etc
            Artisan::call('migrate',[
                '--database' => 'sqlsrv_app',
                '--force' => true
            ]);

            // Crear roles iniciales
$roles = ['SuperAdmin', 'Admin', 'Employee'];
foreach ($roles as $roleName) {
    DB::connection('sqlsrv_app')->table('roles')->updateOrInsert(
        ['name' => $roleName],
        [
            // Genera descripción basada en el nombre del rol, y ocupo operador ternario para simplificar
         'description' => $roleName . ($roleName === 'Admin' 
         ? 'Can edit users' 
         : ($roleName !== 'Employee' ? ' with full access' : ' with view only access')),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]
    );
}

            $migrateOutput = Artisan::output(); // Captura la salida de la migración

            return response()->json(['message' => "Database '{$data['database']}' created successfully.", // y paso la salida de la migración
                                     'migrate_output' => $migrateOutput]); 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
