<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     * Devuelve todas las tareas, ordenadas de la mas nueva a la mas antigua.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::query()
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    /**
     * POST /api/tasks
     * Valida los datos enviados y crea una nueva tarea.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $task = Task::create($validatedData);

        return response()->json($task->refresh(), Response::HTTP_CREATED);
    }

    /**
     * PATCH /api/tasks/{task}
     * Actualiza solo los campos enviados en la peticion.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $task->update($validatedData);

        return response()->json($task->refresh());
    }

    /**
     * DELETE /api/tasks/{task}
     * Elimina una tarea y responde sin contenido.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();

        return response()->noContent();
    }
}
