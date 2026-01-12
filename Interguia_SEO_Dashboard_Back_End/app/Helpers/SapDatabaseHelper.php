<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SapDatabaseHelper
{
    public static function setSapDatabase($host, $port, $username, $password, $database)
    {
        try {
            Config::set('database.connections.sqlsrv', [
                'driver' => 'sqlsrv',
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'encrypt' => 'no',
                'trust_server_certificate' => true,
            ]);

            // Guarda SAP credentials
            $data = compact('host', 'port', 'username', 'password', 'database');
            File::put(storage_path('app/sap_db.json'), json_encode($data, JSON_PRETTY_PRINT));

            return true;
        } catch (\Exception $e) {
            return false; 
        }
    }

    public static function loadSavedConnections()
    {
        try {
            $path = storage_path('app/sap_db.json'); 

            if (!File::exists($path)) {
                return false;
            }

            $data = json_decode(File::get($path), true);

            if (!isset($data['host'], $data['port'], $data['username'], $data['password'], $data['database'])) {
                return false;
            }

            return self::setSapDatabase(
                $data['host'],
                $data['port'],
                $data['username'],
                $data['password'],
                $data['database']
            );
        } catch (\Exception $e) {
            return false;
        }
    }
}
