<div id="todo-create-form" class="card " style="display: none">

    <div class="card-header row">
        <div class="col-6">
            Create Todo
        </div>
        <div class="col-6 text-end">
            <button id="close-create-form-btn" class="btn btn-sm btn-outline-dark">Close</button>
        </div>
    </div><!-- /.card-header -->

    <div class="card-body">
        <div class="my-3">
            <label for="title">Title</label>
            <input type="text" id="title" class="form-control">
        </div><!-- /.my-3 -->
        
        <div class="my-3">
            <label for="title">Description</label>
            <textarea type="text" id="description" class="form-control"></textarea>
        </div><!-- /.my-3 -->

        <button id="store-todo-btn" type="button" class="btn btn-primary">Add Todo</button>
        
    </div><!-- /card-body -->
</div><!-- /.card -->