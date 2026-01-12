<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\SeoDatabaseHelper;

class SeoDatabaseController extends Controller
{
    public function setup(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'database' => 'required|string',
        ]);

        // Define conexion y guarda credentials
        SeoDatabaseHelper::setSeoConnections(
            $data['host'],
            $data['port'],
            $data['username'],
            $data['password'],
            $data['database']
        );

        // Check si existe la base de datos
        $exists = DB::connection('sqlsrv_master')
            ->select("SELECT name FROM sys.databases WHERE name = ?", [$data['database']]);

        if (!empty($exists)) {
            return response()->json(['message' => "Database '{$data['database']}' already exists."]);
        }

        // Crea database
        DB::connection('sqlsrv_master')->statement("CREATE DATABASE [{$data['database']}]");

        return response()->json(['message' => "Database '{$data['database']}' created successfully."]);
    }
}
