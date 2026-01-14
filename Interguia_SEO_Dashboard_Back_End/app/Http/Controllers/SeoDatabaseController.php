<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\SeoDatabaseHelper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class SeoDatabaseController extends Controller
{
    public function setup(Request $request)
    {
        try {
            // Valida datos de conexión
            $data = $request->validate([ // Reglas de validación
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


            // Verifica si la configuración fue exitosa
            if (!$success) {
                return response()->json(['error' => 'Failed to set database connections'], 500);
            }

            // Check si existe la base de datos
            $exists = DB::connection('sqlsrv_master')
                ->select("SELECT name FROM sys.databases WHERE name = ?", [$data['database']]); // Consulta para verificar existencia

            // Si existe, retorna mensaje
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


// Crear usuario Super Admin inicial
$superAdminEmail = 'interguia@gmail.com';


// Genera una contraseña aleatoria de 10 caracteres
$randomPassword = Str::password(10); 

// Evita duplicados si el endpoint se llama otra vez
$superAdmin = User::on('sqlsrv_app')->where('email', $superAdminEmail)->first();

// Si no existe, crea el usuario automaticamente
if (!$superAdmin) {
    $superAdmin = User::on('sqlsrv_app')->create([
        'name' => 'Interguia SuperAdmin',
        'email' => $superAdminEmail,
        'password' => Hash::make($randomPassword), 
        'must_change_password' => true,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);
}

// Asigna rol de SuperAdmin al usuario creado
$superAdminRole = Role::on('sqlsrv_app')
    ->where('name', 'SuperAdmin')
    ->first();

if ($superAdminRole && !$superAdmin->roles()->where('roles.id', $superAdminRole->id)->exists()) {
    $superAdmin->roles()->attach($superAdminRole->id);
}

            $migrateOutput = Artisan::output(); // Captura la salida de la migración

          return response()->json([
          'message' => "Database '{$data['database']}' created successfully.",
          'super_admin' => [
          'email' => $superAdminEmail,
          'temporary_password' => $randomPassword,
          'must_change_password' => true
        ],
          'migrate_output' => $migrateOutput // Retorna la salida de la migración
        ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
