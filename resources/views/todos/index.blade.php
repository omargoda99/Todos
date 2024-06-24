@extends('layouts.app')

@section('content')
<div class="container">
    <div id="success-msg-container" class="alert alert-success my-3" style="display: none"></div>

    <div class="d-flex justify-content-center py-3" style="height: 100px;">
        <div id="spinner-loader" style="display: none" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div id="todos-table" class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">My Todos</div>
                <div class="col-6 text-end">
                    <button id="todo-create-btn" class="btn btn-sm btn-primary">Add Btn</button>
                </div>
            </div><!-- /.row -->
        </div><!-- /.card-header -->

        <div class="card-body" style="height: 360px; overflow-y: scroll">
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

        <div class="row my-2">
            <div class="col-6 text-start">
                <button data-dir="prev-page" class="paginate-action btn btn-dark mx-2">Prev</button>
            </div>
            <div class="col-6 text-end">
                <button data-dir="next-page" class="paginate-action btn btn-dark mx-2">Next</button>
            </div>
        </div>
    </div><!-- /.card -->

    @include('todos.incs._create')

    @include('todos.incs._edit')
    
</div><!-- /.container -->
@endsection

@push('custome-js')
<script>
$(document).ready(function () {

    const Store = (function () {
        let store = {
            todos : [],
            page  : 1 
        };

        const setters = {
            fetchTodosReq    : async () => {
                $('#spinner-loader').fadeIn(500);

                let res = await axios.get(`{{ route('todos.index') }}`, {
                    params : {
                        get_todos : true,
                        page : store.page,
                    }
                });

                $('#spinner-loader').fadeOut(500);

                let { data, success } = res.data;

                store.todos = data.data;

                return [...store.todos];
            },
            
            createNewTodoReq : async (todo_data) => {
                let res = await axios.post(`{{ route('todos.store') }}`, {
                    _token : '{{ csrf_token() }}',
                    ...todo_data
                });

                let { data, success } = res.data;

                if (success) {
                    store.todos.push(data);
                }

                return [...store.todos];
            },

            updateTodoReq : async (todo_id, updated_data) => {
                $('#spinner-loader').fadeIn(500);
                
                let res = await axios.put(`{{ url('todos') }}/${todo_id}`, {
                    _token : '{{ csrf_token() }}',
                    ...updated_data
                });

                $('#spinner-loader').fadeOut(500);

                let { data, success } = res.data;

                if (success) {
                    let index = store.todos.findIndex(todo => todo.id === todo_id);
                    if (index !== -1) {
                        store.todos[index] = data;
                    }
                }

                return [...store.todos];
            },

            deleteTodoReq    : async (todo_id) => {
                $('#spinner-loader').fadeIn(500);

                let res = await axios.post(`{{ url('todos') }}/${todo_id}`, {
                    _token  : '{{ csrf_token() }}',
                    _method : 'DELETE'
                });
                
                $('#spinner-loader').fadeOut(500);

                let { data, success } = res.data;
                
                if (success) {
                    store.todos = store.todos.filter(todo => todo.id != data.id);
                }

                return [...store.todos];
            },

            nextPage : () => {
                store.page = store.todos.length == 0 ? store.page : store.page + 1;
            },

            prevPage : () => {
                store.page = store.page == 1 ? 1 : store.page - 1;
            }
        };
        
        const getters = {
            getTodos : () => {
                return [...store.todos];
            },

            getTodo : (todo_id) => {
                return store.todos.find(todo => todo.id === todo_id);
            }
        };

        return {
            setters,
            getters
        }
    })();

    const View = (function () {
        const fields              = ['title', 'description'];
        const myTodosTableBodyEl  = '#myTodosTableBody';
        const successMsgContainer = '#success-msg-container';

        const renderTodos = (todos_list) => {
            let todos_el = '';

            todos_list.forEach((todo, index) => {
                todos_el += `
                    <tr>
                        <td>${todo.id}</td>
                        <td>${todo.title}</td>
                        <td>${todo.description}</td>
                        <td>
                            <button data-id="${todo.id}" class="delete-btn btn btn-danger btn-sm">Delete</button>
                            <button data-id="${todo.id}" class="edit-btn btn btn-warning btn-sm">Edit</button>
                        </td>
                    </tr>
                `;
            });
            
            $(myTodosTableBodyEl).html(todos_el);
        }

        const renderSuccessMsg = (msg) => {
            $(successMsgContainer).text(msg).slideDown(500);

            setTimeout(() => {
                $(successMsgContainer).text('').slideUp(500);
            }, 4000);
        }

        const toggleCreate = (is_open = true) => {
            if (is_open) {
                $('#todos-table').slideUp(500);
                $('#todo-create-form').slideDown(500);
            } else {
                $('#todos-table').slideDown(500);
                $('#todo-create-form').slideUp(500);
            }
        }

        const toggleUpdate = (is_open = true, todo = null) => {
            if (is_open) {
                $('#todos-table').slideUp(500);
                $('#todo-edit-form').slideDown(500);
                if (todo) {
                    $('#edit-title').val(todo.title);
                    $('#edit-description').val(todo.description);
                    $('#update-todo-btn').data('id', todo.id);
                }
            } else {
                $('#todos-table').slideDown(500);
                $('#todo-edit-form').slideUp(500);
            }
        }

        const getFormData = (prefix = '') => {
            let data      = {};
            let is_valid = true;
           
            fields.forEach(field => {
                let field_val = $(`#${prefix}${field}`).val();
                
                if (Boolean(field_val)) {
                    data[field] = field_val;
                    $(`#${prefix}${field}`).css('border', '');
                } else {
                    is_valid = false;
                    $(`#${prefix}${field}`).css('border', '1px solid red');
                }
            });

            return is_valid ? data : is_valid;
        }

        return {
            toggleCreate,
            renderTodos,
            getFormData,
            renderSuccessMsg,
            toggleUpdate
        };
    })();

    const Controller = (async function () {
        const { setters, getters } = Store;
        
        let todos = await setters.fetchTodosReq();
        View.renderTodos(todos);

        $('#todo-create-btn').on('click', View.toggleCreate);
       
        $('#todo-')

        $('#close-create-form-btn').on('click', () => View.toggleCreate(false));

        $('#store-todo-btn').on('click', async function () {
            let data = View.getFormData();

            if (Boolean(data)) {
                let todos = await setters.createNewTodoReq(data);

                View.renderTodos(todos);

                View.toggleCreate(false);

                View.renderSuccessMsg('You created a new Todo!');
            }
        });

        $('.paginate-action').on('click', async function () {
            let dir = $(this).data('dir');

            dir == 'prev-page' ? setters.prevPage() : setters.nextPage();

            let todos = await setters.fetchTodosReq();
            
            View.renderTodos(todos);
        });

        $('#myTodosTableBody').on('click', '.delete-btn', async function () {
            let flag = confirm('Are you sure you want to delete this todo?');

            if (flag) {
                let todo_id = $(this).data('id');

                let todos   = await setters.deleteTodoReq(todo_id);
                
                View.renderTodos(todos);
            }
        });

        $('#myTodosTableBody').on('click', '.edit-btn', function () {
            let todo_id = $(this).data('id');
            let todo = getters.getTodo(todo_id);

            View.toggleUpdate(true, todo);
        });

        $('#update-todo-btn').on('click', async function () {
            let todo_id = $(this).data('id');
            let data = View.getFormData('edit-');
            let todos = await setters.updateTodoReq(todo_id, data);

            View.renderTodos(todos);

            View.toggleUpdate(false);

            View.renderSuccessMsg('Todo updated successfully!');

        });

        $('#close-edit-form-btn').on('click', () => View.toggleUpdate(false));

    })();

});
</script>
@endpush
