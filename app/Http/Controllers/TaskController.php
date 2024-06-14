<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;  //Task model has access on Tasks table in database
// we use this model to access data in database and show it in index.


class TaskController extends Controller
{
    public function admin()
    {
        return view('admin');
    }
    public function checkAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'password' => 'required|max:255'
        ]);
        $Tasks = Task::all();
        if (auth()->user()->username == "admin" && auth()->user()->password == "admin") {
            return "Done";
        }
    }
    public function index()
    {
        //$Tasks = Task::all();
        // dd(auth()->user()->tasks()->where('completed', false)->orderBy('priority', 'desc')->orderBy('due_date')->get());

        #retreiving data from Tasks table in database 
        $tasks_done = Task::query()
            ->where('user_id', auth()->user()->id)
            ->where('completed', true)
            ->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->get();

        $tasks_not_done = Task::query()
            ->where('user_id', auth()->user()->id)
            ->where('completed', false)
            ->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->get();


        //  Compact is used to pass data to the view.
        return view('tasks/index', compact((['tasks_not_done', 'tasks_done'])));
    }

    public function create()
    {
        return view('/Tasks/create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'priority' => 'required|max:255',
            'due_date' => 'required|max:255',
        ]);

        $data            = $request->all();
        //dd($data);
        $data['user_id'] = auth()->user()->id;
        Task::create($data);

        return redirect()->route('Tasks.index');
    }

    public function edit(Task $task) //the edit funcion will take a Task paramter wich is the row to be edited.
    {
        return view('/Tasks/edit', compact('task'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|max:255',
        ]);

        $task = Task::where('user_id', auth()->user()->id)->find($id);

        if (!isset($task)) {
            session()->flash('failed', 'Not found task !');
            return redirect()->route('Tasks.index');
        }

        $data            = $request->all();
        $data['user_id'] = auth()->user()->id;

        //dd($data);
        $task->update($data);
        return redirect()->route('Tasks.index');
    }

    public function delete($id)
    {
        $task = Task::where('user_id', auth()->user()->id)->find($id);
        if (!isset($task)) {
            session()->flash('failed', 'bad action');
            return redirect()->route('Tasks.index');
        }
        $task->delete();

        return redirect()->route('Tasks.index');
    }

    public function complete(Task $task)
    {
        $task->update([
            'completed' => true,
            'completed_at' => now()
        ]);
        return redirect()->route('Tasks.index');
    }
}
