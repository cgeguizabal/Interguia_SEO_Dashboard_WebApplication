<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Hash; // Para hashear contraseñas
use Laravel\Sanctum\PersonalAccessToken;


class AuthController extends Controller
{
    public function register(Request $request)
    {

    try{
        
    // Valida datos de registro
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',  // Verifica que cada rol exista en la tabla roles
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hashea la contraseña
            'must_change_password' => false
        ]);

        $roleIds = Role::whereIn('name', $data['roles'])->pluck('id')->toArray(); // Obtiene los IDs de los roles seleccionados
        $user->roles()->sync($roleIds); // Asigna los roles al usuario

        $user->load('roles'); // Carga la relación de roles para la respuesta

           return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user)
        ], 201);

        }catch(\Exception $e){
          return response()->json([
            'status' => false,
            'error' => 'Registration failed',
            'message' => $e->getMessage()

                 ], 500);

    }
        
        
        }

    public function login(Request $request)
    {

    try{
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $user->load('roles');
        
        $token = $user->createToken('auth_token')->plainTextToken;

       if ($user->must_change_password) {
    return response()->json([
        'message' => 'Password change required',
        'force_password_change' => true,
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 200);
}


      
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }catch(\Exception $e){
          return response()->json([
            'status' => false,
            'error' => 'Login failed',
            'message' => $e->getMessage()

                 ], 500);}
        
    }

    // Cambio de contraseña de SuperAdmin una vez
    public function changePassword(Request $request) //sanctum captura token y obtiene usuario
    {
        try {
            $request->validate([ // Validación de la nueva contraseña
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user(); // Obtiene el usuario autenticado

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user->password = Hash::make($request->password); // Hashea y actualiza la contraseña
            $user->must_change_password = false; // Marca que ya no necesita cambiar la contraseña
            $user->save(); // Guarda los cambios

            return response()->json([
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
    return response()->json([
        'status' => false,
        'error' => 'Password change failed',
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'trace' => app()->environment('local') ? $e->getTrace() : [] // Solo muestra el trace en entorno
    ], 500);
}

    }



    public function logout(Request $request)
    {
        try{

        // Revoca el token de acceso actual
        $request->user()->currentAccessToken()->delete();

        // Respuesta de éxito
        return response()->json(['message' => 'Logged out']);

        }catch(\Exception $e){
          return response()->json([
            'status' => false,
            'error' => 'Logout failed',
            'message' => $e->getMessage()

                 ], 500);}
        
    }

}
