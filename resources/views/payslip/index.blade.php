@extends('layouts.admin')

@section('page-title')
    {{ __('Payslip') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('payslip') }}</li>
@endsection

@section('content')
    <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12 mt-4">
        @if (Auth::user()->type == 'company' || Auth::user()->type == 'HR')
        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => ['payslip.store'], 'method' => 'POST', 'id' => 'payslip_form']) }}
                <div class="d-flex align-items-end justify-content-end row row-gap-1">
                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('month', __('Select Month'), ['class' => 'form-label']) }}
                            {{ Form::select('month', $month, date('m'), ['class' => 'form-control select', 'id' => 'month']) }}
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('year', __('Select Year'), ['class' => 'form-label']) }}
                            {{ Form::select('year', $year, date('Y'), ['class' => 'form-control select']) }}
                        </div>
                    </div>
                    <div class="col-auto float-end">
                        <a href="#" class="btn  btn-primary"
                            onclick="document.getElementById('payslip_form').submit(); return false;"
                            data-bs-toggle="tooltip" title="{{ __('payslip') }}"
                            data-original-title="{{ __('payslip') }}">{{ __('Generate Payslip') }}
                        </a>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        @endif
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <h5>{{ __('Find Employee Payslip') }}</h5>
                <div class="row flex-1 justify-content-end align-items-center row-gap-1">
                    <div class="col-xxl-2 col-lg-3 col-md-6 col-12">
                        <div class="btn-box">
                            <select class="form-control month_date " name="year" tabindex="-1" aria-hidden="true">
                                <option value="--">--</option>
                                @foreach ($month as $k => $mon)
                                    @php
                                        $selected = date('m') == $k ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $k }}" {{ $selected }}>{{ $mon }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-6 col-12">
                        <div class="btn-box">
                            {{ Form::select('year', $year, date('Y'), ['class' => 'form-control year_date ']) }}
                        </div>
                    </div>
                    <div class="col-auto d-flex gap-3">
                        {{ Form::open(['route' => ['payslip.export'], 'method' => 'POST', 'id' => 'payslip_form']) }}
                        <input type="hidden" name="filter_month" class="filter_month">
                        <input type="hidden" name="filter_year" class="filter_year">
                        <input type="submit" value="{{ __('Export') }}" class="btn btn-primary">
                        {{ Form::close() }}
                        @can('create pay slip')
                            <input type="button" value="{{ __('Bulk Payment') }}" class="btn btn-primary" id="bulk_payment">
                        @endcan
                </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-render-column-cells">
                        <thead>
                            <tr>
                                <th>{{ __('Employee Id') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Payroll Type') }}</th>
                                <th>{{ __('Salary') }}</th>
                                <th>{{ __('Net Salary') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            callback();

            function callback() {
                var month = $(".month_date").val();
                var year = $(".year_date").val();

                $('.filter_month').val(month);
                $('.filter_year').val(year);

                if (month == '') {
                    month = '{{ date('m', strtotime('last month')) }}';
                    year = '{{ date('Y') }}';

                    $('.filter_month').val(month);
                    $('.filter_year').val(year);
                }

                var datePicker = year + '-' + month;

                $.ajax({
                    url: '{{ route('payslip.search_json') }}',
                    type: 'POST',
                    data: {
                        "datePicker": datePicker,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {

                        function renderstatus(data, cell, row) {
                            if (data == 'Paid')
                                return '<div class="badge status_badge bg-success p-2 px-3 rounded"><a href="#" class="text-white">' +
                                    data + '</a></div>';
                            else
                                return '<div class="badge status_badge bg-danger p-2 px-3 rounded"><a href="#" class="text-white">' +
                                    data + '</a></div>';
                        }

                        function renderButton(data, cell, row) {

                            var $div = $(row);
                            employee_id = $div.find('td:eq(0)').text();
                            status = $div.find('td:eq(6)').text();

                            var month = $(".month_date").val();
                            var year = $(".year_date").val();
                            var id = employee_id;
                            var payslip_id = data;

                            var clickToPaid = '';
                            var payslip = '';
                            var view = '';
                            var edit = '';
                            var deleted = '';
                            var form = '';

                            if (data != 0) {
                                var payslip =
                                    '<a href="#" data-url="{{ url('payslip/pdf/') }}/' + id +
                                    '/' + datePicker +
                                    '" data-size="md-pdf"  data-ajax-popup="true" class="btn btn-primary" data-title="{{ __('Employee Payslip') }}">' +
                                    '{{ __('Payslip') }}' + '</a> ';
                            }

                            if (status == "UnPaid" && data != 0) {
                                clickToPaid = '<a href="{{ url('payslip/paysalary/') }}/' + id +
                                    '/' + datePicker + '"  class="view-btn primary-bg btn-sm">' +
                                    '{{ __('Click To Paid') }}' + '</a>  ';
                            }

                            if (data != 0) {
                                view =
                                    '<a href="#" data-url="{{ url('payslip/showemployee/') }}/' +
                                    payslip_id +
                                    '"  data-ajax-popup="true" class="view-btn gray-bg" data-title="{{ __('View Employee Detail') }}">' +
                                    '{{ __('View') }}' + '</a>';
                            }

                            if (data != 0 && status == "UnPaid") {
                                edit =
                                    '<a href="#" data-url="{{ url('payslip/editemployee/') }}/' +
                                    payslip_id +
                                    '"  data-ajax-popup="true" class="view-btn blue-bg" data-title="{{ __('Edit Employee salary') }}">' +
                                    '{{ __('Edit') }}' + '</a>';
                            }

                            var url = '{{ route('payslip.delete', ':id') }}';
                            url = url.replace(':id', payslip_id);

                            @if (\Auth::user()->type != 'Employee')
                                if (data != 0) {
                                    deleted = '<a href="#"  data-url="' + url +
                                        '" class="payslip_delete view-btn red-bg" >' +
                                        '{{ __('Delete') }}' + '</a>';
                                }
                            @endif

                            return view + payslip + clickToPaid + edit + deleted + form;
                        }
                        var tr = '';
                        if (data.length > 0) {
                            $.each(data, function(indexInArray, valueOfElement) {



                                var status =
                                    '<div class="badge status_badge bg-danger p-2 px-3 rounded"><a href="#" class="text-white">' +
                                    valueOfElement[6] + '</a></div>';
                                if (valueOfElement[6] == 'Paid') {
                                    var status =
                                        '<div class="badge status_badge bg-success p-2 px-3 rounded"><a href="#" class="text-white">' +
                                        valueOfElement[6] + '</a></div>';
                                }

                                var id = valueOfElement[0];
                                var employee_id = valueOfElement[1];
                                var payslip_id = valueOfElement[7];

                                if (valueOfElement[7] != 0) {
                                    var payslip =
                                        '<a href="#" data-url="{{ url('payslip/pdf/') }}/' +
                                        id +
                                        '/' + datePicker +
                                        '" data-size="lg"  data-ajax-popup="true" class=" btn-sm btn btn-warning me-1" data-title="{{ __('Employee Payslip') }}" data-bs-toggle="tooltip" title="{{__('Payslip')}}" data-original-title="{{__('Payslip')}}"><i class="ti ti-report-money"></i></a> ';
                                }
                                if (valueOfElement[6] == "UnPaid" && valueOfElement[7] != 0) {
                                    var clickToPaid =
                                        '<a href="{{ url('payslip/paysalary/') }}/' + id +
                                        '/' + datePicker +
                                        '"  class="btn-sm btn btn-primary me-1" "data-bs-toggle="tooltip" title="{{__('Click To Paid')}}" data-original-title="{{__('Click To Paid')}}"><i class="ti ti-currency-dollar"></i></a>  ';
                                } else {
                                    var clickToPaid = '';
                                }

                                if (valueOfElement[7] != 0 && valueOfElement[6] == "UnPaid") {
                                    var edit =
                                        '<a href="#" data-url="{{ url('payslip/editemployee/') }}/' +
                                        payslip_id +
                                        '"  data-ajax-popup="true" class="btn-sm btn btn-info me-2" data-title="{{ __('Edit Employee salary') }}"data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>';
                                } else {
                                    var edit = '';
                                }


                                var url = '{{ route('payslip.delete', ':id') }}';
                                url = url.replace(':id', payslip_id);

                                var deleted = '';
                                @if (\Auth::user()->type != 'Employee')
                                    if (valueOfElement[7] != 0) {
                                        deleted = '<a href="#"  data-url="' + url +
                                            '" class="payslip_delete view-btn btn btn-danger btn-sm"   data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>';
                                    } else {
                                        deleted = '';
                                    }
                                @endif
                                var url_employee = valueOfElement['url'];

                                tr +=
                                    '<tr> ' +
                                    '<td> <a class="btn btn-outline-primary" href="' +
                                    url_employee + '">' +
                                    valueOfElement[1] + '</a></td> ' +
                                    '<td>' + valueOfElement[2] + '</td> ' +
                                    '<td>' + valueOfElement[3] + '</td>' +
                                    '<td>' + valueOfElement[4] + '</td>' +
                                    '<td>' + valueOfElement[5] + '</td>' +
                                    '<td>' + status + '</td>' +
                                    '<td>' + payslip + clickToPaid + edit + deleted + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            // var colspan = $('#pc-dt-render-column-cells thead tr th').length;
                            // var tr = '<tr><td class="dataTables-empty" colspan="' + colspan +
                            //     '">{{ __('No entries found') }}</td></tr>';
                        }

                        $('#pc-dt-render-column-cells tbody').html(tr);
                        new simpleDatatables.DataTable('#pc-dt-render-column-cells');

                    },
                    error: function(data) {

                    }

                });

            }

            $(document).on("change", ".month_date,.year_date", function() {
                callback();
            });

            //bulkpayment Click
            $(document).on("click", "#bulk_payment", function() {
                var month = $(".month_date").val();
                var year = $(".year_date").val();
                var datePicker = year + '_' + month;


            });
            $(document).on('click', '#bulk_payment',
                'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]',
                function() {
                    var month = $(".month_date").val();
                    var year = $(".year_date").val();
                    var datePicker = year + '-' + month;

                    var title = 'Bulk Payment';
                    var size = 'md';
                    var url = 'payslip/bulk_pay_create/' + datePicker;


                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-' + size);
                    $.ajax({
                        url: url,
                        success: function(data) {
                            if (data.length) {
                                $('#commonModal .body').html(data);
                                $("#commonModal").modal('show');
                                // common_bind();
                            } else {
                                show_toastr('error', 'Permission denied.');
                                $("#commonModal").modal('hide');
                            }
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            show_toastr('error', data.error);
                        }
                    });
                });

            $(document).on("click", ".payslip_delete", function() {
                var confirmation = confirm("are you sure you want to delete this payslip?");
                var url = $(this).data('url');


                if (confirmation) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: "JSON",
                        success: function(data) {

                            // show_toastr(data.status, data.msg, 'data.status');
                            show_toastr('success', 'Payslip Deleted Successfully', 'success');


                            setTimeout(function() {
                                location.reload();
                            }, 800)


                        },

                    });

                }
            });
        });
    </script>
@endpush
