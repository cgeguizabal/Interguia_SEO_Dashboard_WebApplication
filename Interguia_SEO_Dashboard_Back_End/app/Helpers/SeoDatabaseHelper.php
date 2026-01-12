<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeoDatabaseHelper
{

    // Define las conexiones de base de datos SEO_dashboard y sqlsrv_master
    public static function setSeoConnections($host, $port, $username, $password, $database) //Argumentos a recibir en la funcion
    {
        // Conexion a la base de datos master para crear nuevas bases de datos
        Config::set('database.connections.sqlsrv_master', [
            'driver' => 'sqlsrv',
            'host' => $host, 
            'port' => $port,
            'database' => 'master',
            'username' => $username,
            'password' => $password,
            'trust_server_certificate' => true,
        ]);

        // conexion para la base de datos SEO_dashboard real cuando ya ha sido creada
        Config::set('database.connections.sqlsrv_app', [
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

        // Hace que la conexion por defecto sea sqlsrv_app entonces en cada model hay que definir si se usara otra conexion
        DB::setDefaultConnection('sqlsrv_app');

        // Guarda permanentemente en storage/app/seo_db.json para usar cada vez que se inicie la aplicacion
        $data = [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'database' => $database
        ];

        File::put(storage_path('app/seo_db.json'), json_encode($data, JSON_PRETTY_PRINT));
    }


   // Carga las credenciales guardadas y define las conexiones
    public static function loadSavedConnections()
    {
        $path = storage_path('app/seo_db.json'); // Ruta del archivo donde se guardan las credenciales

        if (!File::exists($path)) { // Si el archivo no existe, retorna false
            return false;
        }

        $data = json_decode(File::get($path), true); // Lee y decodifica el archivo JSON

         // Define las conexiones usando las credenciales guardadas

        self::setSeoConnections(
            $data['host'],
            $data['port'],
            $data['username'],
            $data['password'],
            $data['database']
        );

        return true;
    }
}
