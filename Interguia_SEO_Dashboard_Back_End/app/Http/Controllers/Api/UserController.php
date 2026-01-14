<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Para hashear contraseñas
use App\Http\Resources\UserResource; // Aqui en UserResource he definido la estructura de los datos que regreso

class UserController extends Controller
{
    public function index() // Obtiene todos los usuarios con roles
    {

    try{

     $users = User::with('roles')->get(); // Obtiene usuarios con roles

        // Handle error si no hay usuarios
        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => UserResource::collection($users) // Usa UserResource para regresar datos en orden
        ], 200);

    }catch(\Exception $e){ // Manejo de errores
        return response()->json([
            'status' => false,
            'error' => 'Failed to fetch users',
        'message' => $e->getMessage()], 500);
    }
       
    }

    
    public function show(string $id) // Obtiene un usuario por ID con roles
    {
    try{

    $user = User::with('roles')->findOrFail($id); // Busca usuario con el id y carga roles

        return response()->json([ // Respuesta exitosa
            'status' => true,
            'data' => new UserResource($user)
        ]);

    }catch(\Exception $e){ // Manejo de errores

     return response()->json([
      'status' => false,
      'error' => 'Failed to fetch user',
      'message' => $e->getMessage()], 500);
      }
        
    }

    
    public function update(Request $request, string $id) // Actualiza un usuario y sus roles
   {

   try{

    $user = User::with('roles')->findOrFail($id);// Busca usuario por id y carga roles

    $data = $request->validate([ // Valida datos de entrada
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        'password' => 'sometimes|string|min:6',
        'roles' => 'sometimes|array',
        'roles.*' => 'exists:roles,name', 
    ]);

    // actualiza password si se proporciona
    if (isset($data['password']) && $data['password'] !== null && $data['password'] !== '') {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']); // previene que se establezca a null al no querer actualizar
    }

    // actualiza usuario
    $user->update(
        collect($data)->except('roles')->toArray() 
    );

    // Sincroniza roles si se proporcionan
    if (array_key_exists('roles', $data)) {
        $roleIds = \App\Models\Role::whereIn('name', $data['roles'])->pluck('id')->toArray(); // Obtiene IDs de roles
        $user->roles()->sync($roleIds);// Sincroniza roles
    }

    // recarga relaciones
    $user->load('roles');

    return response()->json([
        'status' => true,
        'data' => new UserResource($user)
    ]);


   }catch(\Exception $e){
        return response()->json([
        'status' => false,
        'error' => 'Failed to update user',
        'message' => $e->getMessage()], 500);
   }
    
}


   //Elimina un usuario y sus roles
    public function destroy(string $id)
    {
        try{

         $user = User::with('roles')->findOrFail($id);

        // Elimina relaciones de roles
        $user->roles()->detach();

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
        
        }catch(\Exception $e){
         return response()->json([
            'status' => false,
            'error' => 'Failed to delete user',
            'message' => $e->getMessage()], 500);
        }
       
    }
}