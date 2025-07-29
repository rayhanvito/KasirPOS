{{Form::open(array('url'=>'loan','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
{{ Form::hidden('employee_id',$employee->id, array()) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('loan_option', __('Loan Options'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('loan_option',$loan_options,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create loan option here.') }} <a href="{{ route('loanoption.index') }}"><b>{{ __('Create loan option') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('type', $loan, null, ['class' => 'form-control select amount_type', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Loan Amount'),['class'=>'form-label amount_label']) }}<x-required></x-required>
            {{ Form::number('amount',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Amount'))) }}
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('reason',null, array('class' => 'form-control ','required'=>'required','rows' => 3, 'placeholder'=>__('Enter Reason'))) }}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
