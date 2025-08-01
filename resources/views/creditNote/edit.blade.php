
{{ Form::model($creditNote, array('route' => array('invoice.edit.credit.updatenote',$creditNote->invoice, $creditNote->id), 'method' => 'post', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('credit_note', __('Credit Note'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::text('credit_note', !empty($creditNote->creditNote) ? \App\Models\CustomerCreditNotes::creditNumberFormat($creditNote->creditNote->credit_id) : '',array('class'=>'form-control ','required'=>'required' , 'disabled'))}}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control ','required'=>'required','placeholder'=>'Select Issue Date','max' => date('Y-m-d')))}}
            </div>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="form-icon-user">
                {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01','min'=>'0')) }}
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3','placeholder' => 'Enter Description']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

<script>
    $(document).ready(function () {
        var amount = parseFloat($('#amount').val());
        var creditNoteId = "{{ $creditNote->credit_note }}";
        $.ajax({
            url: "{{ route('credit-note.price') }}",
            method: 'POST',
            data: {
                credit_note: creditNoteId,
                amount:amount,
                _token: "{{ csrf_token() }}"
            },
            success: function (data) {
                if (data !== undefined) {
                    $('input[name="amount"]').attr('max', data);
                    $('input[name="amount"]').attr('min', 0);
                }
            }
        });
    });
</script>