<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Spatie\Permission\Models\Role;

class TaskController extends Controller
{
    /**
     * Create a New Task.
     */
    public function createTask(TaskRequest $request)
    {
        try {
            $request->validated();
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => auth()->user()->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Task Created',
                'task' => $task
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve task by Id.
     */
    public function retrieveTask($id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invaild Task'
                ], 404);
            }

            $user = Task::where('user_id', auth()->user()->id)->first();
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task can not be accessed by this user'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'task' => $task
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Get All Task for a Specific Logged in User
     */
    public function retrieveAllTask()
    {
        try {
            $user = auth()->user();
            $tasks = Task::where('user_id', $user->id)->get();

            if ($tasks->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No tasks available for this user'
                ], 200);
            }
            return response()->json([
                'status' => true,
                'tasks' => $tasks
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Update the task by Id.
     */
    public function updateTask(Request $request, Task $task)
    {
        try {
            $request->validate([
                'title' => 'string|max:255|nullable',
                'description' => 'string|nullable'
            ]);

            $user = auth()->user();
            if ($task->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You do not have permission to update this task'
                ], 401);
            }

            // Update the task attributes if provided in the request
            if ($request->filled('title')) {
                $task->title = $request->input('title');
            }

            if ($request->filled('description')) {
                $task->description = $request->input('description');
            }

            $task->save();

            return response()->json([
                'status' => true,
                'message' => 'Task updated successfully',
                'task' => $task
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Delete a task.
     */
    public function deleteTask(Task $task)
    {
        try {
            $user = auth()->user();

            if ($task->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You do not have permission to delete this task'
                ], 401);
            }

            $task->delete();

            return response()->json([
                'status' => true,
                'message' => 'Task deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}