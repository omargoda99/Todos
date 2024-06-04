@extends('layouts.app')

@section('content')
<div class="container">
    <div id="todos-table" class="card">
        <div class="card-header row">
            <div class="col-6">
                My Todos
            </div>
            <div class="col-6 text-end">
                <button id="todo-create-btn" class="btn btn-sm btn-primary">Add Btn</button>
            </div>
        </div><!-- /.card-header -->

        <div class="card-body" style="height: 400px; overflow-y: scroll">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="myTodosTableBody"></tbody>
            </table>
        </div><!-- /.card-body -->
    </div><!-- /.card -->

    @include('todos.incs._create')
    
</div><!-- /.container -->
@endsection


@push('custome-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.2/axios.min.js" integrity="sha512-JSCFHhKDilTRRXe9ak/FJ28dcpOJxzQaCd3Xg8MyF6XFjODhy/YMCM8HW0TFDckNHWUewW+kfvhin43hKtJxAw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$('document').ready(function () {

    const Store = (() => {
        const meta = {
            todos: [
                {id: 1, title: 'Todo 1', description: 'Todo 1'},
                {id: 2, title: 'Todo 2', description: 'Todo 2'},
                {id: 3, title: 'Todo 3', description: 'Todo 3'},
            ]
        };

        const getters = {
            getTodos: () => [...meta.todos]
        };

        const setters = {
            createTodo: async (data) => {
                const newId = meta.todos.length ? meta.todos[meta.todos.length - 1].id + 1 : 1;
                const newTodo = {id: newId, ...data};
                meta.todos.push(newTodo);
                return newTodo;
            },
            updateTodo: async (id, data) => {
                const todoIndex = meta.todos.findIndex(todo => todo.id === id);
                if (todoIndex !== -1) {
                    meta.todos[todoIndex] = {id, ...data};
                    return meta.todos[todoIndex];
                }
                return null;
            },
            deleteTodo: async (id) => {
                meta.todos = meta.todos.filter(todo => todo.id !== id);
            }
        };

        return {
            getters,
            setters
        }
    })();

    const View = (() => {
        const tableId = '#myTodosTableBody';
        const formEls = ['title', 'description'];
        let currentTodoId = null;

        const renderTable = (todos) => {
            let todos_el = '';

            todos.forEach((todo, index) => {
                todos_el += `
                    <tr data-id="${todo.id}">
                        <td>${index + 1}</td>
                        <td>${todo.title}</td>
                        <td>${todo.description}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-todo-btn">Edit</button>
                            <button class="btn btn-sm btn-danger delete-todo-btn">Delete</button>
                        </td>
                    </tr>
                `;
            });

            $(tableId).html(todos_el);
        }

        const toggleTodoCreateForm = (open = true) => {
            if (open) {
                $('#todos-table').slideUp(500);
                $('#todo-create-form').slideDown(500);
            } else {
                $('#todos-table').slideDown(500);
                $('#todo-create-form').slideUp(500);
            }
        }

        const getFormData = () => {
            let data = {};
            let is_valied = true;

            formEls.forEach(el => {
                let tmp = $(`#${el}`).val();
                if (Boolean(tmp)) {
                    data[el] = tmp;
                    $(`#${el}`).css('border', '');
                } else {
                    is_valied = false;
                    $(`#${el}`).css('border', '1px solid red');
                }
            })

            return is_valied ? data : false;
        }

        const populateForm = (todo) => {
            $('#title').val(todo.title);
            $('#description').val(todo.description);
        }

        const clearForm = () => {
            $('#title').val('');
            $('#description').val('');
            currentTodoId = null;
        }

        return {
            renderTable,
            toggleTodoCreateForm,
            getFormData,
            populateForm,
            clearForm
        }
    })();

    (() => {
        $('#store-todo-btn').on('click', async function () {
            let data = View.getFormData();

            if (data) {
                if (currentTodoId) {
                    let updatedTodo = await Store.setters.updateTodo(currentTodoId, data);
                    if (updatedTodo) {
                        View.renderTable(Store.getters.getTodos());
                        View.toggleTodoCreateForm(false);
                        View.clearForm();
                    }
                } else {
                    let newTodo = await Store.setters.createTodo(data);
                    if (newTodo) {
                        View.renderTable(Store.getters.getTodos());
                        View.toggleTodoCreateForm(false);
                        View.clearForm();
                    }
                }
            }
        });

        // Show Create Form
        $('#todo-create-btn').on('click', function () {
            View.clearForm();
            View.toggleTodoCreateForm();
        })

        // Close Create Form
        $('#close-create-form-btn').on('click', function () {
            View.clearForm();
            View.toggleTodoCreateForm(false);
        })

        // Delegate event for delete button
        $(document).on('click', '.delete-todo-btn', async function () {
            const todoId = $(this).closest('tr').data('id');
            await Store.setters.deleteTodo(todoId);
            View.renderTable(Store.getters.getTodos());
        });

        // Delegate event for edit button
        $(document).on('click', '.edit-todo-btn', function () {
            const todoId = $(this).closest('tr').data('id');
            const todos = Store.getters.getTodos();
            const todo = todos.find(t => t.id === todoId);

            if (todo) {
                currentTodoId = todo.id;
                View.populateForm(todo);
                View.toggleTodoCreateForm();
            }
        });

        // Initial render
        View.renderTable(Store.getters.getTodos());
    })();
});
</script>
@endpush
