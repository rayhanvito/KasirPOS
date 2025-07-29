{{ Form::open(array('route' => array('task.bug.store',$project_id), 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
                            $user = \App\Models\User::find(\Auth::user()->creatorId());
                    $plan= \App\Models\Plan::getPlan($user->plan);
    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['project bug']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('title', '', array('class' => 'form-control','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('priority', $priority, null,array('class' => 'form-control select','required'=>'required')) !!}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::date('start_date', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::date('due_date', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Bug Status'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('status', $status, null,array('class' => 'form-control select','required'=>'required')) !!}
            <div class="text-xs mt-1">
                {{ __('Create bug status here.') }} <a href="{{ route('bugstatus.index') }}"><b>{{ __('Create bug status') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('assign_to', __('Assigned To'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('assign_to', $users, null,array('class' => 'form-control select','required'=>'required')) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2', 'placeholder'=>__('Enter Description')]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
