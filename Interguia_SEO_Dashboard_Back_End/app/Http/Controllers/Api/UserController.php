<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    
    public function index() // Obtiene todos los usuarios con roles
    {
        $users = User::with('roles')->get();

        // Handle error
        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => UserResource::collection($users)
        ], 200);
    }

    
    public function show(string $id) // Obtiene un usuario por ID con roles
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => new UserResource($user)
        ]);
    }

    



    
    public function update(Request $request, string $id) // Actualiza un usuario y sus roles
   {
    $user = User::with('roles')->findOrFail($id);

    $data = $request->validate([
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
        $roleIds = \App\Models\Role::whereIn('name', $data['roles'])->pluck('id')->toArray();
        $user->roles()->sync($roleIds);
    }

    // recarga relaciones
    $user->load('roles');

    return response()->json([
        'status' => true,
        'data' => new UserResource($user)
    ]);
}


   //Elimina un usuario y sus roles
    public function destroy(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Elimina relaciones de roles
        $user->roles()->detach();

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}