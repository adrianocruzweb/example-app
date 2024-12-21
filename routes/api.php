<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [
    AuthController::class, 'register'
]);

Route::post('login', [
    AuthController::class, 'login'
]);

Route::post('logout', [
    AuthController::class, 'logout'
])->middleware('auth:api');

Route::get('/test-db-connection', function () {
    try {
        $user = DB::table('users')->first(); // Tente obter um usuário da tabela 'users'
        return response()->json(['message' => 'Conexão com o banco de dados bem-sucedida!', 'data' => $user], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Falha na conexão: ' . $e->getMessage()], 500);
    }
});

Route::post('todos', [
    TodoController::class, 'store'
])->middleware('auth:api');

Route::get('todos', [
    TodoController::class, 'indexAll'
])->middleware('auth:api');

Route::put('todos/{id}', [
    TodoController::class, 'update'
])->middleware('auth:api');

Route::get('todos/{id}', [
    TodoController::class, 'getOne'
])->middleware('auth:api');

Route::delete('todos/{id}', [
    TodoController::class, 'destroy'
])->middleware('auth:api');