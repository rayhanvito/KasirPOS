{{Form::open(array('url'=>'saturationdeduction','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="modal-body">

    {{ Form::hidden('employee_id',$employee->id, array()) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('deduction_option', __('Deduction Options'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('deduction_option',$deduction_options,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create deduction option here.') }} <a href="{{ route('deductionoption.index') }}"><b>{{ __('Create deduction option') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('title',null, array('class' => 'form-control ','required'=>'required', 'placeholder'=>__('Enter Title'))) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('type', $saturationdeduc, null, ['class' => 'form-control select amount_type', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label amount_label']) }}<x-required></x-required>
            {{ Form::number('amount',null, array('class' => 'form-control ','required'=>'required','step'=>'0.01', 'placeholder'=>__('Enter Amount'))) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

    {{ Form::close() }}
