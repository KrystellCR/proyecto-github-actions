<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_the_task_interface(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Laravel Task API')
            ->assertSee('/api/tasks');
    }

    public function test_it_creates_a_task(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Preparar GitHub Actions',
            'description' => 'Crear workflow de CI para Laravel',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('title', 'Preparar GitHub Actions')
            ->assertJsonPath('is_completed', false);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Preparar GitHub Actions',
            'is_completed' => false,
        ]);
    }

    public function test_it_lists_tasks(): void
    {
        Task::create(['title' => 'Escribir pruebas']);

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'Escribir pruebas']);
    }

    public function test_it_marks_a_task_as_completed(): void
    {
        $task = Task::create(['title' => 'Desplegar en Render']);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'is_completed' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('is_completed', true);

        $this->assertTrue($task->fresh()->is_completed);
    }
}
