<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;

class TodoController extends Controller
{

    public function index()
    {
        // Retorna todos os to-dos do usuário autenticado
        return Todo::where('user_id', Auth::id())->get();
    }

    public function indexAll()
    {
        $todos = Todo::all()->toJson(JSON_PRETTY_PRINT); // Obtém todos os registros de to-dos
        return response($todos, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
            'responsible' => 'required|string|in:Alice,Adriano,Sarah', // Validação para responsáveis fictícios
            'stage' => 'required|string|in:to-do,doing,test,done', // Validação para o campo stage
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => $request->completed ?? false,
            'responsible' => $request->responsible,
            'stage' => $request->stage,
            'user_id' => Auth::id(),
        ]);

        return response()->json($todo, 201);
    }


    public function show(Todo $todo)
    {
        // Verifica se o to-do pertence ao usuário autenticado
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($todo);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);

        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
            'responsible' => 'required|string|in:Alice,Adriano,Sarah', // Validação para responsáveis fictícios
            'stage' => 'required|string|in:to-do,doing,test,done', // Validação para o campo stage
        ]);

        $todo->update($request->only(['title', 'description', 'completed', 'responsible', 'stage']));

        return response()->json($todo,201);
    }

    public function getOne($id)
    {
        try {
            $todo = Todo::findOrFail($id);

            return response()->json($todo,200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(),500);
        }
    }

    public function destroy($id)
    {
        try {
            $todo = Todo::findOrFail($id);

            $todo->delete();
            return response()->json(['message' => 'To-do removido com sucesso']);
        } catch (Exception $e) {
            return response()->json($e->getMessage(),500);
        }
    }
}