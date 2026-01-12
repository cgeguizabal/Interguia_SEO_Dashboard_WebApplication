<?php

namespace App\Http\Controllers;

use App\Helpers\SapDatabaseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SapDatabaseController extends Controller
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
        SapDatabaseHelper::setSapDatabase(
            $data['host'],
            $data['port'],
            $data['username'],
            $data['password'],
            $data['database']
        );

        return response()->json(['message' => "SAP Database connection settings saved successfully."]);

       
    }
}
