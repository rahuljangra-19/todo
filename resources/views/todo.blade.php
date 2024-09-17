@extends('layouts.app')

@section('title', 'TO-DO')

@section('content')
<section class="vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col">
                <div class="card" id="list1" style="border-radius: .75rem; background-color: #eff1f2;">
                    <div class="card-body py-4 px-4 px-md-5">
                        <p class="h1 text-center mt-3 mb-4 pb-3 text-primary">
                            <i class="fas fa-check-square me-1"></i>
                            <u>My Todo-s</u>
                        </p>
                        <div class="pb-2">
                            <div class="card">
                                <div class="card-body p-2" id="input-wrap">
                                    <form action="javascript:void(0)" method="post" id="taskForm">
                                        @csrf
                                        <div class="d-flex flex-row align-items-center">
                                            <input type="text" class="form-control form-control-lg" id="task" name="task" placeholder="Add new...">
                                            <div>
                                                <button type="submit" class="btn btn-primary" id="submitBtn">Add</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mb-4 pt-2 pb-3">
                            <!-- filter -->
                            <p class="small mb-0 me-2 text-muted">Filter</p>
                            <select class="select form-control select-input w-25" name="filter" id="filter">
                                <option value="1">All</option>
                                <option value="2">Completed</option>
                                <option value="3" selected>Uncompleted</option>
                            </select>
                        </div>
                        <hr class="my-4">
                        <!-- Tasks wrap -->
                        <div id="task-wrap">
                            <!-- task component -->
                            <x-task :tasks="$data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        toastr.options = {
            'closeButton': true,
            'newestOnTop': true,
            'progressBar': true,
            'positionClass': 'toast-top-right',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '2000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        }

        var taskForm = $('#taskForm');
        var submitBtn = $('#submitBtn');

        // submit task form
        taskForm.submit(function(e) {
            e.preventDefault();
            if (validateTask()) {
                submitBtn.attr('disabled', true);
                $.ajax({
                    url: "{{ URL('task') }}",
                    method: "POST",
                    data: taskForm.serialize(),
                    success: function(result) {
                        if (result.status === 200) {
                            toastr.success(result.message);
                            $('#task-wrap').prepend(result.data);
                            taskForm[0].reset();
                        } else if (result.status === 400) {
                            toastr.warning(result.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    },
                    complete: function() {
                        submitBtn.attr('disabled', false);
                    }
                });
            }
        });

        $('#task').focus(function() {
            $('#input-wrap').removeClass('invalid');
        });

        function validateTask() {
            let task = $('#task').val().trim();
            if (task === '') {
                $('#input-wrap').addClass('invalid');
                return false;
            }
            $('#input-wrap').removeClass('invalid');
            return true;
        }


        // Action handler for tasks (complete/ Incomplete status update)
        $(document).on('click', '.toggleTask', function() {
            const taskId = $(this).data('id');
            const action = $(this).prop('checked') ? 'completed' : 'incomplete';

            $.ajax({
                url: `{{ URL('task') }}/${taskId}`,
                method: 'PUT',
                data: {
                    action: action,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.status === 200 && action === 'completed') {
                        $('#task-item-' + taskId).remove();
                    }
                    toastr[result.status === 200 ? 'success' : 'warning'](result.message);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });

        // Delete a task with confirmation
        $(document).on('click', '.deleteTask', function() {
            const taskId = $(this).data('id');
            const confirmDelete = confirm('Are you sure you want to delete this task?');

            if (confirmDelete) {
                $.ajax({
                    url: `{{ URL('task') }}/${taskId}`,
                    method: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        if (result.status === 200) {
                            $('#task-item-' + taskId).remove();
                        }
                        toastr[result.status === 200 ? 'success' : 'warning'](result.message);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        });

        // tasks filter
        document.getElementById('filter').addEventListener('change', function() {
            $.ajax({
                url: `{{ URL('get-data') }}`,
                method: 'GET',
                data: {
                    filter: this.value
                },
                success: function(result) {
                    if (result.status === 200) {
                        $('#task-wrap').html(result.data)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });
    });
</script>

@endpush