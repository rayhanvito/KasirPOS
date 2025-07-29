@extends('layouts.admin')
@section('page-title')
    {{__('Cash Flow')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Cash Flow')}}</li>
@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{$currentYear}}';
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush

@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}" data-original-title="{{__('Download')}}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a>
    </div>
@endsection


@section('content')




    <div class="row justify-content-center">
        <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(array('route' => array('report.quarterly.cashflow'),'method' => 'GET','id'=>'quarterly_cashflow')) }}
                        <div class="col-xl-12">

                            <div class="row justify-content-between align-items-center">
                                <div class="col-xl-3">
                                    <ul class="nav nav-ul nav-pills gap-2" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-home-tab" data-bs-toggle="pill"
                                               href="{{ route('report.monthly.cashflow') }}"
                                               onclick="window.location.href = '{{ route('report.monthly.cashflow') }}'" role="tab"
                                               aria-controls="pills-home" aria-selected="true">{{ __('Monthly') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill" href="#" role="tab"
                                               aria-controls="pills-profile" aria-selected="false">{{ __('Quarterly') }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-xl-9">
                                    <div class="row justify-content-end align-items-center">
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('year', __('Year'),['class'=>'form-label'])}}
                                                {{ Form::select('year',$yearList,isset($_GET['year'])?$_GET['year']:'', array('class' => 'form-control select')) }}
                                            </div>
                                        </div>

                                        <div class="col-auto mt-4">
                                            <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('quarterly_cashflow').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>
                                            <a href="{{route('report.quarterly.cashflow')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"  title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                                <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
        </div>
    </div>

    <div id="printableArea">
        <div class="row mt-1">
            <div class="col-12 col-sm-6 mb-4">
                <input type="hidden" value="{{__('Quarterly Cashflow').' '.'Report of'.' '.$filter['startDateRange'].' to '.$filter['endDateRange']}}" id="filename">
                <div class="card report-card mb-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.09766 0.761719V20.668C3.09766 21.089 3.43835 21.4297 3.85938 21.4297H17.5703C17.9913 21.4297 18.332 21.089 18.332 20.668V0.761719C18.332 0.340691 17.9913 0 17.5703 0H3.85938C3.43835 0 3.09766 0.340691 3.09766 0.761719ZM15.2852 17.5703H12.2383C11.8173 17.5703 11.4766 17.2296 11.4766 16.8086C11.4766 16.3876 11.8173 16.0469 12.2383 16.0469H15.2852C15.7062 16.0469 16.0469 16.3876 16.0469 16.8086C16.0469 17.2296 15.7062 17.5703 15.2852 17.5703ZM6.14453 3.85938H10.7148C11.1359 3.85938 11.4766 4.20007 11.4766 4.62109C11.4766 5.04212 11.1359 5.38281 10.7148 5.38281H6.14453C5.7235 5.38281 5.38281 5.04212 5.38281 4.62109C5.38281 4.20007 5.7235 3.85938 6.14453 3.85938ZM6.14453 6.90625H15.2852C15.7062 6.90625 16.0469 7.24694 16.0469 7.66797C16.0469 8.089 15.7062 8.42969 15.2852 8.42969H6.14453C5.7235 8.42969 5.38281 8.089 5.38281 7.66797C5.38281 7.24694 5.7235 6.90625 6.14453 6.90625ZM6.14453 9.95312H15.2852C15.7062 9.95312 16.0469 10.2938 16.0469 10.7148C16.0469 11.1359 15.7062 11.4766 15.2852 11.4766H6.14453C5.7235 11.4766 5.38281 11.1359 5.38281 10.7148C5.38281 10.2938 5.7235 9.95312 6.14453 9.95312ZM6.14453 13H15.2852C15.7062 13 16.0469 13.3407 16.0469 13.7617C16.0469 14.1827 15.7062 14.5234 15.2852 14.5234H6.14453C5.7235 14.5234 5.38281 14.1827 5.38281 13.7617C5.38281 13.3407 5.7235 13 6.14453 13Z"
                                    fill="white" />
                                <path
                                    d="M8.42969 26H22.1406C22.5617 26 22.9023 25.6593 22.9023 25.2383V5.38281C22.9023 4.96179 22.5617 4.62109 22.1406 4.62109H19.8555V20.668C19.8555 21.9281 18.8304 22.9531 17.5703 22.9531H7.66797V25.2383C7.66797 25.6593 8.00866 26 8.42969 26Z"
                                    fill="white" />
                            </svg>
                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{ __('Report') }}</h5>
                            <p class="text-muted mb-0">{{__('Quarterly Cashflow')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 mb-4">
                <div class="card report-card mb-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="report-icon">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.6667 5.22996V2.94866C22.3111 2.57293 22.75 1.8819 22.75 1.08332V0.541684C22.75 0.242277 22.5077 0 22.2083 0H3.79168C3.49228 0 3.25 0.242277 3.25 0.541684V1.08337C3.25 1.8819 3.68885 2.57293 4.33332 2.94871V5.22996C4.33332 7.37653 5.24845 9.43104 6.84384 10.8667L9.21416 13L6.84384 15.1333C5.24845 16.569 4.33332 18.6235 4.33332 20.77V23.0513C3.68885 23.4271 3.25 24.1181 3.25 24.9167V25.4584C3.25 25.7577 3.49228 26 3.79168 26H22.2084C22.5078 26 22.7501 25.7577 22.7501 25.4583V24.9166C22.7501 24.1181 22.3112 23.4271 21.6667 23.0513V20.77C21.6667 18.6235 20.7516 16.569 19.1562 15.1333L16.7858 13L19.1562 10.8667C20.7516 9.43104 21.6667 7.37648 21.6667 5.22996ZM17.7068 9.25646L14.442 12.1949C14.2135 12.4002 14.0833 12.6927 14.0833 13C14.0833 13.3073 14.2135 13.5999 14.442 13.8051L17.7068 16.7435C18.8462 17.7692 19.5 19.2371 19.5 20.77V22.75H18.1456L13.4332 16.4669C13.229 16.1939 12.7709 16.1939 12.5667 16.4669L7.85444 22.75H6.5V20.77C6.5 19.2371 7.15381 17.7692 8.29324 16.7435L11.5581 13.805C11.7866 13.5998 11.9167 13.3073 11.9167 12.9999C11.9167 12.6926 11.7866 12.4001 11.5581 12.1949L8.29324 9.25641C7.15381 8.23078 6.5 6.76289 6.5 5.22996V3.25H19.5V5.22996C19.5 6.76289 18.8462 8.23078 17.7068 9.25646Z" fill="white"/>
                                <path d="M16.7337 7.58331H9.26621C9.05197 7.58331 8.85783 7.70976 8.77109 7.90547C8.68436 8.10174 8.72082 8.33026 8.86524 8.48895L12.6368 11.9685C12.74 12.0622 12.8701 12.1087 13.0002 12.1087C13.1303 12.1087 13.2605 12.0621 13.3636 11.9685L17.1346 8.48895C17.279 8.33026 17.3155 8.10174 17.2288 7.90547C17.1421 7.70976 16.9479 7.58331 16.7337 7.58331Z" fill="white"/>
                                </svg>

                        </div>
                        <div class="report-info flex-1">
                            <h5 class="mb-1">{{ __('Duration') }}</h5>
                            <p class="text-muted mb-0">{{ 'Jan-2025 to Dec-2025' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="pb-3">{{__('Income')}}</h5>
                                <div class="table-responsive mt-3 mb-3">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th width="25%">{{__('Category')}}</th>
                                            @foreach($month as $m)
                                                <th width="15%">{{$m}}</th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="13" class="font-bold"><span>{{__('Revenue : ')}}</span></td>
                                        </tr>
                                        @if(!empty($revenueIncomeArray))
                                            @foreach($revenueIncomeArray as $i=>$revenue)
                                                <tr>
                                                    <td>{{$revenue['category']}}</td>
                                                    @foreach($revenue['amount'] as $j=>$amount)
                                                        <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endif

                                        <tr>
                                            <td colspan="13" class="font-bold"><span>{{__('Invoice : ')}}</span></td>
                                        </tr>
                                        @if(!empty($invoiceIncomeArray))
                                            @foreach($invoiceIncomeArray as $i=>$invoice)
                                                <tr>
                                                    <td>{{$invoice['category']}}</td>
                                                    @foreach($invoice['amount'] as $j=>$amount)
                                                        <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endif
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush border">
                                                    <tbody>
                                                    <tr>
                                                        <td colspan="13" class="font-bold"><span>{{__('Total Income =  Revenue + Invoice ')}}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="25%" class="text-dark">{{__('Total Income')}}</td>
                                                        @foreach($totalIncome as $income)
                                                            <td width="15%">{{\Auth::user()->priceFormat($income)}}</td>
                                                        @endforeach
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-sm-12">
                                    <h5>{{__('Expense')}}</h5>
                                    <div class="table-responsive mt-4">
                                        <table class="table mb-0" >
                                            <thead>
                                            <tr>
                                                <th width="25%">{{__('Category')}}</th>
                                                @foreach($month as $m)
                                                    <th width="15%">{{$m}}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td colspan="13" class="font-bold"><span>{{__('Payment : ')}}</span></td>
                                            </tr>
                                            @if(!empty($expenseArray))
                                                @foreach($expenseArray as $i=>$expense)
                                                    <tr>
                                                        <td>{{$expense['category']}}</td>
                                                        @foreach($expense['amount'] as $j=>$amount)
                                                            <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="13" class="font-bold"><span>{{__('Bill : ')}}</span></td>
                                            </tr>
                                            @if(!empty($billExpenseArray))
                                                @foreach($billExpenseArray as $i=>$bill)
                                                    <tr>
                                                        <td>{{$bill['category']}}</td>
                                                        @foreach($bill['amount'] as $j=>$amount)
                                                            <td width="15%">{{\Auth::user()->priceFormat($amount)}}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-flush border" >
                                                        <tbody>
                                                        <tr>
                                                            <td colspan="13" class="font-bold"><span>{{__('Total Expense =  Payment + Bill ')}}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-dark">{{__('Total Expenses')}}</td>
                                                            @foreach($totalExpense as $expense)
                                                                <td width="15%">{{\Auth::user()->priceFormat($expense)}}</td>
                                                            @endforeach
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush border" >
                                                <tbody>
                                                    <thead>
                                                    <tr>
                                                        <th colspan="13" class="font-bold"><span>{{__('Net Profit = Total Income - Total Expense ')}}</span></th>
                                                    </tr>
                                                    </thead>
                                                    <tr>
                                                        <td width="25%" class="text-dark">{{__('Net Profit')}}</td>
                                                        @foreach($netProfitArray as $i=>$profit)
                                                            <td width="15%"> {{\Auth::user()->priceFormat($profit)}}</td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


