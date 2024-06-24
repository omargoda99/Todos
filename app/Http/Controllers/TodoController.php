<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\Todo;

class TodoController extends Controller
{
    private $targetModel;

    public function __construct () {
        $this->targetModel = new Todo;
    }

    public function index (Request $request) {

        if (isset($request->get_todos)) {
            $todos = Todo::paginate(5);

            return response()->json(['success' => isset($todos), 'data' => $todos, 'err' => !isset($todos) ? 'Todos not found' : null]);
        }

        return view('todos.index');
    }

    public function create () {}

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|max:255',
            'description' => 'required|max:9999'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => null, 'errors' => $validator->errors()]);
            // return response()->json($validator->errors(), 403);
        }

        $todo = Todo::create($request->all());

        return response()->json(['success' => true, 'data' => $todo]);
        // return response()->json($todo, 201);
    }

    public function show ($id) {}
    
    public function edit ($id) {}

    public function update(Request $request, $id) {

    $request->validate([
      'title'       => 'required|max:255',
      'description' => 'required|max:9999'
    ]);

    $post = Post::find($id);

    $post->update($request->all());

    return redirect()->route('todos.index')
      ->with('success', 'Todo updated successfully.');

    }

    public function destroy ($id) {
        $todo = Todo::find($id);

        if (!isset($todo)) {
            return response()->json(['success' => false, 'data' => null, 'err' => 'Not found todo']);
        }

        $todo->delete();

        return response()->json(['success' => true, 'data' => $todo]);
    }

}
