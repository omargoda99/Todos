@extends('layouts.app')
@section('title')
TMS
@endsection
@section('content')
<div class="container text-center">
    <h4>Tasks List</h4>
</div>

@if(session()->has('failed'))
<div class="alert aler-danger">
    {{ session('failed') }}
</div>
@endif

<div class="container text-center">
    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3 float-left">Create A New Task</a>
    <table class="table table-bordered  table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th style="width: 250px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks_not_done as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->description }}</td>
                <td>{{ $task->priority }}</td>
                <td>{{ $task->due_date }}</td>
                <td>
                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                    <form action="{{ route('tasks.delete', $task->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm""><i class=" fa fa-trash"></i> Delete</button>
                        <!-- when you click delete button will send the Task id using delete method to route tasks.delete -->
                    </form>

                    <form action="{{ route('tasks.complete', $task->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check"></i> Complete
                        </button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<br>
<div class="container text-center">
    <h4> Completed Tasks</h4>
</div>

<div class="container text-center">
    <table class="table table-bordered  table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks_done as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->description }}</td>
                <td>{{ $task->priority }}</td>
                <td>{{ $task->due_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>



    <script src=" https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @endsection