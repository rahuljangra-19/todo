<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getData(Request $request)
    {
        $data = $this->getFilterData($request);
        $html = view('components.task', ['tasks' => $data]);
        return response()->json(['status' => 200, 'message' => 'Task data', 'data' => $html->render()]);
    }

    public function getFilterData($request)
    {
        $data = Task::query();
        $data->when($request->filter, function ($data) use ($request) {
            if ($request->filter == 2) {
                $data = $data->where('is_completed', true);
            }
            if ($request->filter == 3) {
                $data = $data->where('is_completed', false);
            }
        });
        $data->when(!$request->filter, function ($data) use ($request) {
            $data = $data->where('is_completed', false);
        });

        $data = $data->latest()->get();
        return $data;
    }

    public function index()
    {
        $data = $this->getFilterData(new Request());
        return view('todo', ['data' => $data]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateTaskRequest($request);
        try {
            $task = new Task();
            $task->title = $request->task;
            $task->save();
            $tasks[] = $task;
            $html = view('components.task', ['tasks' => $tasks]);
            return response()->json(['status' => 200, 'message' => 'Task created successfully', 'data' => $html->render()]);
        } catch (Exception $e) {
            Log::warning($e->getMessage());
            return response()->json(['status' => 400, 'message' => 'Something went wrong']);
        }
    }

    public function validateTaskRequest($request)
    {
        $request->validate([
            'task' => 'required|unique:tasks,title',
        ], [
            'task.required' => 'The task field is required.',
            'task.unique' => 'This task has already been created!',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        try {
            if ($request->action === 'completed') {
                $task->is_completed = true;
                $task->save();
            }
            if ($request->action === 'incomplete') {
                $task->is_completed = false;
                $task->save();
            }
            return response()->json(['status' => 200, 'message' => 'Task updated successfully', 'data' => null]);
        } catch (Exception $e) {
            Log::warning($e->getMessage());
            return response()->json(['status' => 400, 'message' => 'Something went wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return response()->json(['status' => 200, 'message' => 'Task deleted successfully', 'data' => null]);
        } catch (Exception $e) {
            Log::warning($e->getMessage());
            return response()->json(['status' => 400, 'message' => 'Something went wrong']);
        }
    }
}
