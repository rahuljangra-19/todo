@inject('carbon','Carbon\Carbon' )
@foreach ($tasks as $task)

<ul class="list-group list-group-horizontal rounded-0 " id="task-item-{{$task->id}}">
    <li class="list-group-item d-flex align-items-center ps-0 pe-3 py-1 rounded-0 border-0 bg-transparent">
        <div class="form-check">
            <input class="form-check-input me-0 toggleTask" type="checkbox" value="" data-id="{{ $task->id }}" id="task_{{$task->id}}" aria-label="..." {{$task->is_completed?'checked':''}} />
        </div>
    </li>
    <li class="list-group-item px-3 py-1 d-flex align-items-center flex-grow-1 border-0 bg-transparent">
        <p class="lead fw-normal mb-0">{{ $task->title }}</p>
    </li>
    <li class="list-group-item ps-3 pe-0 py-1 rounded-0 border-0 bg-transparent">
        <div class="d-flex flex-row justify-content-end mb-1">
            <button class=" btn btn-danger btn-wrap deleteTask" data-id="{{ $task->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="text-end text-muted">
            <a href="#!" class="text-muted" data-mdb-toggle="tooltip" title="Created date">
                <p class="small mb-0"><i class="fas fa-info-circle me-2"></i>{{ $carbon::parse($task->created_at)->format('d M, Y') }}
                </p>
            </a>
        </div>
    </li>
</ul>
@endforeach