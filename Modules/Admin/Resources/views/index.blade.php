@extends('base::layouts.mt-main')

@section('page-title') <?= $pageTitle; ?> @endsection
@section('page-sub-title') <?= $pageSubTitle; ?> @endsection

@section('content')

    <div class="card card-custom">
        <div class="row border-bottom mb-7">

            <div class="col-md-6">
                <div class="card card-custom">
                    <form name="searchorder" action="{{ url('/admin/fetch-channel-orders') }}" method="POST" id="fetch_api_orders_form">
                        @csrf
                        <div class="card-body">
                            <div class="form-group mb-8">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <select class="form-control" id="api_channel" name="api_channel" >
                                            @foreach($availableApiChannels as $apiChannel)
                                                <option value="{{ $apiChannel['id'] }}">
                                                    {{ $apiChannel['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <input type='text' class="form-control" name="api_channel_dates" id="api_channel_dates" readonly placeholder="Select Delivery Date Range" type="text"/>
                                        <input  type="hidden" value="{{ date('Y-m-d') }}" id="api_channel_date_start" name="api_channel_date_start" />
                                        <input  type="hidden" value="{{ date('Y-m-d') }}" id="api_channel_date_end" name="api_channel_date_end" />
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary mr-2">Fetch Orders From Server</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom">
                    <form name="searchorder" action="{{ url('/admin/find-order') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group mb-8">
                                <div class="form-group row">
                                    <label  class="col-2 col-form-label">Search Order</label>
                                    <div class="col-8">
                                        <input class="form-control" type="text" value="" placeholder="Order Number" id="order_number" name="order_number" />
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-primary mr-2">Search</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{--<div class="card-body p-0 mb-7">
            <!--begin: Wizard-->
            <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
                <!--begin: Wizard Nav-->
                <div class="wizard-nav">
                    <div class="wizard-steps">
                        <!--begin::Wizard Step 1 Nav-->
                        <div class="wizard-step" data-wizard-type="step" <?php if($selectedEmirate=='DXB'){?> data-wizard-state="current" <?php } ?>>
                            <a class="wizard-label" href="{{ url('/admin') }}?emirate=DXB">
                                <h3 class="wizard-title">
                                    Dubai
                                </h3>
                                <div class="wizard-bar"></div>
                            </a>
                        </div>
                        <!--end::Wizard Step 1 Nav-->
                        <!--begin::Wizard Step 2 Nav-->
                        <div class="wizard-step" data-wizard-type="step" <?php if($selectedEmirate=='SHJ'){?> data-wizard-state="current" <?php } ?>>
                            <a class="wizard-label" href="{{ url('/admin') }}?emirate=SHJ">
                                <h3 class="wizard-title">
                                    Sharjah
                                </h3>
                                <div class="wizard-bar"></div>
                            </a>
                        </div>
                        <!--end::Wizard Step 2 Nav-->
                        <!--begin::Wizard Step 3 Nav-->
                        <div class="wizard-step" data-wizard-type="step" <?php if($selectedEmirate=='AJM'){?> data-wizard-state="current" <?php } ?>>
                            <a class="wizard-label" href="{{ url('/admin') }}?emirate=AJM">
                                <h3 class="wizard-title">
                                    Ajman
                                </h3>
                                <div class="wizard-bar"></div>
                            </a>
                        </div>
                        <!--end::Wizard Step 3 Nav-->
                        <!--begin::Wizard Step 4 Nav-->
                        <div class="wizard-step" data-wizard-type="step" <?php if($selectedEmirate=='AUH'){?> data-wizard-state="current" <?php } ?>>
                            <a class="wizard-label" href="{{ url('/admin') }}?emirate=AUH">
                                <h3 class="wizard-title">
                                    Abu Dhabi
                                </h3>
                                <div class="wizard-bar"></div>
                            </a>
                        </div>
                        <!--end::Wizard Step 4 Nav-->
                    </div>
                </div>
                <!--end: Wizard Nav-->
            </div>
            <!--end: Wizard-->
        </div>--}}

    </div>

    <div class="card card-custom">

        <div class="row border-bottom mb-7">

            <div class="col-md-12">

                <div class="card card-custom gutter-b">

                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Sale Orders Filters</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col col-12">
                                <form name="salesChartForm" action="{{ url('/admin/filter-orders') }}" method="POST" id="fetch_admin_sale_orders_form">
                                    @csrf
                                    <div class="form-group mb-8">
                                        <div class="form-group row">
                                            <div class="col-4">
                                                <select class="form-control datatable-input" id="api_channel_filter" name="api_channel_filter" >
                                                    <option value="">All Channels</option>
                                                    @foreach($availableApiChannels as $apiChannel)
                                                        <option value="{{ $apiChannel['id'] }}">
                                                            {{ $apiChannel['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control datatable-input" id="emirates_region_filter" name="emirates_region_filter" >
                                                    <option value="">All Emirates</option>
                                                    @foreach($emirates as $key => $emirate)
                                                        <option value="{{ $key }}">
                                                            {{ $emirate }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select class="form-control datatable-input" id="order_status_filter" name="order_status_filter" >
                                                    <option value="" >All Order Statuses</option>
                                                    @foreach($availableStatuses as $statusKey => $statusEl)
                                                        <option value="{{ $statusKey }}" >{{ $statusEl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-4">
                                                <input type='text' class="form-control" name="delivery_date_range_filter" id="delivery_date_range_filter" readonly placeholder="Select Delivery Date Range" type="text"/>
                                                <input  type="hidden" class="datatable-date-input" value="{{ date('Y-m-d') }}" id="delivery_date_start_filter" name="delivery_date_start_filter" />
                                                <input  type="hidden" class="datatable-date-input" value="{{ date('Y-m-d') }}" id="delivery_date_end_filter" name="delivery_date_end_filter" />
                                            </div>
                                            <div class="col-lg-4">
                                                <select class="form-control datatable-input" id="delivery_slot_filter" name="delivery_slot_filter" >
                                                    <option value="" >All Time Slots</option>
                                                    @foreach($deliveryTimeSlots as $deliveryEl)
                                                        <option value="{{ $deliveryEl }}" >{{ $deliveryEl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4 text-right">
                                                <button type="button" id="filter_admin_order_filter_btn" class="btn btn-primary btn-lg mr-2">
                                                    <span><i class="la la-search"></i>Search</span>
                                                </button>
                                                <button type="button" id="filter_admin_order_reset_btn" class="btn btn-primary btn-lg mr-2">
                                                    <span><i class="la la-close"></i>Reset</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="card card-custom">

        <div class="row border-bottom mb-7">

            <div class="col-md-6">

                <div class="card card-custom gutter-b">

                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Sale Orders Sales Chart</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row" id="sale_order_sales_chart_card_row">
                            <div class="col col-12">
                                <div id="sale_orders_sales_bar_chart"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-md-6">

                <div class="card card-custom gutter-b">

                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Sale Orders Status Chart</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row" id="sale_order_status_chart_card_row">
                            <div class="col col-12">
                                <div id="sale_orders_status_bar_chart"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="card card-custom">

        <div class="row border-bottom mb-7">

            <div class="col-md-12">

                <div class="card card-custom gutter-b">

                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Sale Orders List</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col col-12">

                                <div class="table-responsive text-center" id="admin_order_filter_table_area">
                                    <table class="table table-bordered" id="admin_order_filter_table">

                                        <thead>
                                        <tr>
                                            <th># Order Id</th>
                                            <th>Channel</th>
                                            <th>Emirates</th>
                                            <th>Customer Name</th>
                                            <th>Delivery Date</th>
                                            <th>Delivery Schedule Interval</th>
                                            <th>Picker</th>
                                            <th>Picked At</th>
                                            <th>Driver</th>
                                            <th>Delivered At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    {{--<div class="card card-custom">

        <div class="row border-bottom mb-7">
            <div class="col-md-7">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label"><?php echo $emirates[$selectedEmirate]?></h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <!-- <thead><tr><th class="pt-1 pb-9 pl-0 font-weight-bolder text-muted font-size-lg text-uppercase">Description</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Hours</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Rate</th><th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase">Amount</th></tr></thead> -->
                            <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Delivery Schedule Interval</th>
                                <th>Count</th>
                                <th>Export</th>
                            </tr>

                            <?php foreach($regionOrderCount as $record)
                            {
                            ?>
                            <tr>
                                <td><?php echo $record['delivery_date']?></td>
                                <td><?php echo $record['delivery_time_slot']?></td>
                                <td>
                                    <a href="{{ url('/admin/delivery-details') }}?region=<?php echo urlencode($selectedEmirate);?>&interval=<?php echo urlencode($record['delivery_time_slot']);?>&date=<?php echo urlencode($record['delivery_date']);?>">
                                        <?php echo $record['total_orders']?>
                                    </a>
                                </td>
                                <td><a href="{{ url('/admin/download-items-schedule-csv') }}?region=<?php echo urlencode($selectedEmirate);?>&date=<?php echo urlencode($record['delivery_date']);?>&interval=<?php echo urlencode($record['delivery_time_slot']);?>">Export</a> </td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Drivers</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <!-- <thead><tr><th class="pt-1 pb-9 pl-0 font-weight-bolder text-muted font-size-lg text-uppercase">Description</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Hours</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Rate</th><th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase">Amount</th></tr></thead> -->
                            <tbody>
                            <tr>
                                <th>S.No.</th>
                                <th>Driver Name</th>
                                <th>Orders Count</th>
                            </tr>
                            <?php
                            if(!empty($driverData)) {
                            $i =1;
                            foreach($driverData as $data) {
                            ?>
                            <tr>
                                <td><?php echo $i?></td>
                                <td><a href="{{ url('/admin/driver-details') }}?sn=<?php echo urlencode($i);?>&date=<?php echo urlencode($todayDate);?>&driver=<?php echo urlencode($data['wing_order_driver']);?>&count=<?php echo urlencode($data['count_of_orders']);?>"> <?php echo $data['wing_order_driver']?> </a></td>
                                <td><?= $data['count_of_orders']?></td>
                            </tr>
                            <?php
                            $i++;
                            }
                            } else { ?>
                            <tr>
                                <td colspan="4" style="text-align: center">No Drivers Found !!</td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 border-left-md  text-left">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Export Items Date Wise</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <!-- <thead><tr><th class="pt-1 pb-9 pl-0 font-weight-bolder text-muted font-size-lg text-uppercase">Description</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Hours</th><th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Rate</th><th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase">Amount</th></tr></thead> -->
                            <tbody>
                            <tr>
                                <th> Date</th>
                                <th></th>
                            </tr>
                            <?php for($i = -3; $i<10 ; $i++) {
                            $date = date('Y-m-d', strtotime(' +'.$i.' day'));
                            ?>
                            <tr>
                                <td>
                                    <a href="{{ url('/admin/download-items-date-csv') }}?region=<?php echo urlencode($selectedEmirate);?>&date=<?php echo urlencode($date);?>"><?= $date;?></a>
                                </td>
                                <td>
                                    <a href="{{ url('/admin/delivery-details') }}?region=<?php echo urlencode($selectedEmirate);?>&interval=na&date=<?php echo urlencode($date);?>">
                                        View Orders
                                    </a>
                                </td>
                            </tr>
                            <?php

                            } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>--}}

@endsection

@section('custom-js-section')

    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            AdminCustomJsBlocks.indexPage('{{ url('/') }}');
        });
    </script>

@endsection
