{{ Form::open(['route' => ['job.on.board.store', $id], 'method' => 'post', 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        @if ($id == 0)
            <div class="form-group col-md-12">
                {{ Form::label('application', __('Interviewer'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('application', $applications, null, ['class' => 'form-control select2', 'required' => 'required']) }}
            </div>
        @else
            {{ Form::hidden('application', $id, null, ['class' => 'form-control', 'required' => 'required']) }}
        @endif
        <div class="form-group col-md-12">
            {!! Form::label('joining_date', __('Joining Date'), ['class' => 'col-form-label']) !!}<x-required></x-required>
            {!! Form::date('joining_date', null, ['class' => 'form-control ','autocomplete'=>'off', 'required' => 'required']) !!}
        </div>

        <div class="form-group col-md-6">
            {!! Form::label('days_of_week', __('Days Of Week'), ['class' => 'col-form-label']) !!}<x-required></x-required>
            {!! Form::number('days_of_week', null, ['class' => 'form-control ','autocomplete'=>'off','min'=>'0', 'required' => 'required', 'placeholder'=>__('Days Of Week')]) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('salary', __('Salary'), ['class' => 'col-form-label']) !!}<x-required></x-required>
            {!! Form::number('salary', null, ['class' => 'form-control ','autocomplete'=>'off','min'=>'0', 'required' => 'required', 'placeholder'=>__('Salary')]) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('salary_type', __('Salary Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('salary_type', $salary_type, null, ['class' => 'form-control select', 'required' => 'required']) }}
            <div class="text-xs mt-1">
                {{ __('Create salary type here.') }} <a href="{{ route('paysliptype.index') }}"><b>{{ __('Create salary type') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('salary_duration', __('Salary Duration'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('salary_duration', $salary_duration, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('job_type', __('Job Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('job_type', $job_type, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('status', $status, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
