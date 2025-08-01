@extends('layouts.admin')
@section('page-title')
    {{ __('Journal Entry Create') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Double Entry') }}</li>
    <li class="breadcrumb-item">{{ __('Journal Entry') }}</li>
@endsection

@push('script-page')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>

    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                    // for item SearchBox ( this function is  custom Js )
                    JsSearchBox();

                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".debit");
                        var totalDebit = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            totalDebit = parseFloat(totalDebit) + parseFloat($(inputs[i]).val());
                        }
                        $('.totalDebit').html(totalDebit.toFixed(2));


                        var inputs = $(".credit");
                        var totalCredit = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            totalCredit = parseFloat(totalCredit) + parseFloat($(inputs[i]).val());
                        }
                        $('.totalCredit').html(totalCredit.toFixed(2));


                    }
                },
                ready: function(setIndexes) {
                    // $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');

            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    changeItem(tr.find('.item'));
                }
            }

        }

        $(document).on('keyup', '.debit', function() {
            var el = $(this).parent().parent().parent().parent();
            var debit = $(this).val();

            // Validate input: Allow only integers or floats
            if (!/^\d*\.?\d*$/.test(debit)) {
                $(this).val(0);
                return;
            }

            var credit = 0;
            el.find('.credit').val(credit);
            el.find('.amount').html(debit);


            var inputs = $(".debit");
            var totalDebit = 0;
            for (var i = 0; i < inputs.length; i++) {
                totalDebit = parseFloat(totalDebit) + parseFloat($(inputs[i]).val());
            }
            $('.totalDebit').html(totalDebit.toFixed(2));

            el.find('.credit').attr("disabled", true);
            if (debit == '') {
                el.find('.credit').attr("disabled", false);
            }
        })

        $(document).on('keyup', '.credit', function() {
            var el = $(this).parent().parent().parent().parent();
            var credit = $(this).val();

            // Validate input: Allow only integers or floats
            if (!/^\d*\.?\d*$/.test(credit)) {
                $(this).val(0);
                return;
            }

            var debit = 0;
            el.find('.debit').val(debit);
            el.find('.amount').html(credit);

            var inputs = $(".credit");
            var totalCredit = 0;
            for (var i = 0; i < inputs.length; i++) {
                totalCredit = parseFloat(totalCredit) + parseFloat($(inputs[i]).val());
            }
            $('.totalCredit').html(totalCredit.toFixed(2));

            el.find('.debit').attr("disabled", true);
            if (credit == '') {
                el.find('.debit').attr("disabled", false);
            }
        })
    </script>
@endpush

@section('action-btn')
    @php
        $user = \App\Models\User::find(\Auth::user()->creatorId());
        $plan = \App\Models\Plan::getPlan($user->plan);
    @endphp
    @if ($plan->chatgpt == 1)
        <div class="float-end">
            <a href="#" data-size="md" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['journal entry']) }}" data-bs-placement="top"
                data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{ __('Generate with AI') }}</span>
            </a>
        </div>
    @endif
@endsection

@section('content')
    {{ Form::open(['url' => 'journal-entry', 'class' => 'w-100', 'class'=>'needs-validation', 'novalidate']) }}
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('journal_number', __('Journal Number'), ['class' => 'form-label']) }}
                                <input type="text" class="form-control"
                                    value="{{ \Auth::user()->journalNumberFormat($journalId) }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('date', __('Transaction Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::date('date', null, ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
                                {{ Form::text('reference', '', ['class' => 'form-control' , 'placeholder'=>__('Enter Reference')]) }}
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <div class="form-group">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => '2' , 'placeholder'=>__('Enter Description')]) }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card repeater">
                <div class="item-section p-4 text-end">
                            <a href="#" data-repeater-create="" class="btn btn-primary " data-toggle="modal"
                                data-target="#add-bank">
                                <i class="ti ti-plus"></i> {{ __('Add Accounts') }}
                            </a>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0" data-repeater-list="accounts" id="sortable-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Account') }}<x-required></x-required></th>
                                    <th>{{ __('Debit') }}<x-required></x-required></th>
                                    <th>{{ __('Credit') }}<x-required></x-required></th>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Amount') }} </th>
                                    <th width="2%"></th>
                                </tr>
                            </thead>

                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                    <td width="25%" class="form-group pt-0">
                                        <select name="account" class="form-control" required="required">
                                            <option value="">{{ __('Select Chart of Account') }}</option>
                                            @foreach ($chartAccounts as $typeName => $subtypes)
                                                <optgroup label="{{ $typeName }}">
                                                    @foreach ($subtypes as $subtypeId => $subtypeData)
                                                        <option disabled style="color: #000; font-weight: bold;">{{ $subtypeData['account_name'] }}</option>
                                                        @foreach ($subtypeData['chart_of_accounts'] as $chartOfAccount)
                                                            <option value="{{ $chartOfAccount['id'] }}">
                                                                &nbsp;&nbsp;&nbsp;{{ $chartOfAccount['account_name'] }}
                                                            </option>
                                                            @foreach ($subtypeData['subAccounts'] as $subAccount)
                                                                @if ($chartOfAccount['id'] == $subAccount['parent_account'])
                                                                <option value="{{ $subAccount['id'] }}" class="ms-5"> &nbsp; &nbsp;&nbsp;&nbsp; {{' - '. $subAccount['account_name'] }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="form-group price-input">
                                            {{ Form::text('debit', '', ['class' => 'form-control debit', 'required' => 'required', 'placeholder' => __('Debit'), 'required' => 'required']) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group price-input">
                                            {{ Form::text('credit', '', ['class' => 'form-control credit', 'required' => 'required', 'placeholder' => __('Credit'), 'required' => 'required']) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            {{ Form::text('description', '', ['class' => 'form-control', 'placeholder' => __('Description')]) }}
                                        </div>
                                    </td>
                                    <td class="text-end amount">0.00</td>
                                    <td>
                                        <div class="action-btn me-2">
                                            <a href="#!" class="ti ti-trash text-white btn btn-sm repeater-action-btn bg-danger ms-2" data-repeater-delete data-bs-toggle="tooltip" title="{{ __('Delete') }}"></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ __('Total Credit') }}
                                            ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalCredit">0.00</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-end"><strong>{{ __('Total Debit') }}
                                            ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                    <td class="text-end totalDebit">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer d-flex align-items-center gap-2 mb-3">
        <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('journal-entry.index') }}';"
            class="btn btn-secondary">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}
@endsection
