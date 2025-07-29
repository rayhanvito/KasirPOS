@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Job Application') }}
@endsection
@php
    $logo = \App\Models\Utility::get_file('uploads/avatar/');
    $profile = \App\Models\Utility::get_file('uploads/job/profile/');
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}" id="main-style-link">
@endpush
@push('script-page')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>

    <script>
        $(document).on('change', '#jobs', function() {2

            var id = $(this).val();

            $.ajax({
                url: "{{ route('get.job.application') }}",
                type: 'POST',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var job = JSON.parse(data);
                    var applicant = job.applicant;
                    var visibility = job.visibility;
                    var question = job.custom_question;

                    (applicant.indexOf("gender") != -1) ? $('.gender').removeClass('d-none'): $(
                        '.gender').addClass('d-none');
                    (applicant.indexOf("dob") != -1) ? $('.dob').removeClass('d-none'): $('.dob')
                        .addClass('d-none');
                    (applicant.indexOf("country") != -1) ? $('.country').removeClass('d-none'): $(
                        '.country').addClass('d-none');

                    (visibility.indexOf("profile") != -1) ? $('.profile').removeClass('d-none'): $(
                        '.profile').addClass('d-none');
                    (visibility.indexOf("resume") != -1) ? $('.resume').removeClass('d-none'): $(
                        '.resume').addClass('d-none');
                    (visibility.indexOf("letter") != -1) ? $('.letter').removeClass('d-none'): $(
                        '.letter').addClass('d-none');

                    $('.question').addClass('d-none');

                    if (question.length > 0) {
                        question.forEach(function(id) {
                            $('.question_' + id + '').removeClass('d-none');
                        });
                    }


                }
            });
        });

        @can('move job application')
            ! function(a) {
                "use strict";

                var t = function() {
                    this.$body = a("body")
                };
                t.prototype.init = function() {
                    a('[data-plugin="dragula"]').each(function() {

                        var t = a(this).data("containers"),

                            n = [];
                        if (t)
                            for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                        else n = [a(this)[0]];
                        var r = a(this).data("handleclass");
                        r ? dragula(n, {
                            moves: function(a, t, n) {
                                return n.classList.contains(r)
                            }
                        }) : dragula(n).on('drop', function(el, target, source, sibling) {
                            var order = [];
                            $("#" + target.id + " > div").each(function() {
                                order[$(this).index()] = $(this).attr('data-id');
                            });

                            var id = $(el).attr('data-id');

                            var old_status = $("#" + source.id).data('status');
                            var new_status = $("#" + target.id).data('status');
                            var stage_id = $(target).attr('data-id');


                            $("#" + source.id).parent().find('.count').text($("#" + source.id +
                                " > div").length);
                            $("#" + target.id).parent().find('.count').text($("#" + target.id +
                                " > div").length);
                            $.ajax({
                                url: '{{ route('job.application.order') }}',
                                type: 'POST',
                                data: {
                                    application_id: id,
                                    stage_id: stage_id,
                                    order: order,
                                    new_status: new_status,
                                    old_status: old_status,
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    show_toastr('success',
                                        'Job-application successfully updated',
                                        'success');
                                },
                                error: function(data) {
                                    data = data.responseJSON;
                                    show_toastr('error', data.error, 'error')
                                }
                            });
                        });
                    })
                }, a.Dragula = new t, a.Dragula.Constructor = t
            }(window.jQuery),
            function(a) {
                "use strict";

                a.Dragula.init()

            }(window.jQuery);
        @endcan
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Job Application') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">

        @can('create job application')
            <a href="#" data-size="lg" data-url="{{ route('job-application.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create New Job Application') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['job-application.index'], 'method' => 'get', 'id' => 'applicarion_filter']) }}

                        <div class="row d-flex align-items-end justify-content-end row-gap-1">

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('start_date', $filter['start_date'], ['class' => 'month-btn form-control ']) }}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('end_date', $filter['end_date'], ['class' => 'month-btn form-control ']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('job', __('Job'), ['class' => 'form-label']) }}
                                    {{ Form::select('job', $jobs, $filter['job'], ['class' => 'form-control select']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end">

                                <a href="#" class="btn btn-sm btn-primary me-1"
                                    onclick="document.getElementById('applicarion_filter').submit(); return false;"
                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('job-application.index') }}" class="btn btn-sm btn-danger"
                                    data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                                </a>
                            </div>

                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden mt-0">
        <div class="container-kanban">
            @php

                $json = [];
                foreach ($stages as $stage) {
                    $json[] = 'task-list-' . $stage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards" data-plugin="dragula"
                data-containers='{!! json_encode($json) !!}'>
                @foreach ($stages as $stage)
                    @php $applications = $stage->applications($filter) @endphp

                    <div class="col">
                        <div class="crm-sales-card mb-4">

                            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                                <h4 class="mb-0">{{ $stage->title }}</h4>
                                <span class="f-w-600 count">{{ count($applications) }}</span>
                            </div>

                            <div class="sales-item-wrp kanban-box" id="task-list-{{ $stage->id }}"
                                data-id="{{ $stage->id }}">
                                @foreach ($applications as $application)
                                    <div class="card" data-id="{{ $application->id }}">
                                        <div class="card-header border-0 pb-0 position-relative">
                                            <h5><a
                                                    href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}">{{ $application->name }}</a>
                                            </h5>
                                            <div class="card-header-right">
                                                @if (Auth::user()->type != 'client')
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu icon-dropdown dropdown-menu-end">
                                                            @can('show job application')
                                                                <a class="dropdown-item"
                                                                    href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}"
                                                                    class="dropdown-item"> <i class="ti ti-eye"></i> <span>
                                                                        {{ __('View') }} </span> </a>
                                                            @endcan
                                                            @can('delete job application')
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['job-application.destroy', $application->id],
                                                                    'id' => 'delete-form-' . $application->id,
                                                                ]) !!}
                                                                <a href="#!" class="dropdown-item bs-pass-para"><i
                                                                        class="ti ti-trash"></i>
                                                                    <span> {{ __('Delete') }} </span>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endcan


                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <ul class="list-inline mb-0 mt-0">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-12">
                                                            <span class="static-rating static-rating-sm d-block mb-2">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= $application->rating)
                                                                        <i class="star fas fa-star voted"></i>
                                                                    @else
                                                                        <i class="star fas fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <small
                                                        class="text-md d-block mb-2">{{ !empty($application->jobs) ? $application->jobs->title : '' }}</small>


                                                    <li class="list-inline-item d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" title="{{ __('Applied at') }}">
                                                        <i class="ti ti-clock me-1" data-ajax-popup="true"
                                                            data-title="{{ __('Applied at') }}"></i>{{ \Auth::user()->dateFormat($application->created_at) }}

                                                    </li>

                                                    <li class="list-inline-item d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" title="{{ __('Source') }}">
                                                    </li>
                                                </ul>
                                                <div class="user-group">

                                                    <img src="{{ !empty($application->profile) ? $profile . $application->profile : $logo . 'avatar.png' }}"
                                                        class="hweb rounded border-2 border border-primary">


                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <span class="empty-container" data-placeholder="Empty"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
