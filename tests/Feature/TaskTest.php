<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

     public function testCreateTask()
    {
        // Create a user
        $user = User::factory()->create();

        // Assign the user to the "user" role
        $user->assignRole('client');

        // Request input
        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
        ];

        // Send a POST request to the endpoint
        $response = $this->actingAs($user)
            ->postJson('/api/create/task', $data);

        // Assert the response status code and JSON structure
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Task Created',
            ]);

        // Assert that the task is created in the database
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'user_id' => $user->id,
        ]);
    }

    public function testRetrieveTask()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole('client');
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Valid task ID and authorized user
        $response = $this->actingAs($user)
            ->getJson('/api/retrieve/task/' . $task->id);

        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertTrue($responseData['status']);
        $this->assertArrayHasKey('task', $responseData);
        $this->assertEquals($task->id, $responseData['task']['id']);

        // Invalid task ID
        $invalidTaskId = 999; // Assume a non-existing task ID
        $response = $this->actingAs($user)
            ->getJson('/api/retrieve/task/' . $invalidTaskId);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Invaild Task', // Updated expected message
            ]);

        //  Unauthorized user
        $otherUser = User::factory()->create();
        $otherUser->assignRole('client');
        $response = $this->actingAs($otherUser)
            ->getJson('/api/retrieve/task/' . $task->id);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Task can not be accessed by this user',
            ]);
    }

    public function testRetrieveAllTask()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole('client');

        // User has no tasks
        $response = $this->actingAs($user)
            ->getJson('/api/retrieve/all/task');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'No tasks available for this user',
            ]);

        // Create tasks associated with the user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/retrieve/all/task');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'tasks' => $tasks->toArray(),
            ]);
    }

    public function testUpdateTask()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole("client");
        
        // Create a task for the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Update task data
        $data = [
            'title' => 'Updated Task Title',
            'description' => 'Updated task description.',
        ];

        // Send a PUT request to the endpoint
        $response = $this->actingAs($user)
            ->putJson('/api/update/task/' . $task->id, $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Task updated successfully',
                'task' => [
                    'id' => $task->id,
                    'title' => 'Updated Task Title',
                    'description' => 'Updated task description.',
                    'user_id' => $user->id,
                ],
            ]);

        // Assert that the task is updated in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated task description.',
            'user_id' => $user->id,
        ]);
    }

    public function testDeleteTask()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole('client');
        // Create a task for the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Send a DELETE request to the endpoint
        $response = $this->actingAs($user)
            ->deleteJson('/api/delete/task/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Task deleted successfully',
            ]);

        // Assert that the task is deleted from the database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}