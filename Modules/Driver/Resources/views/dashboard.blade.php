@extends('base::layouts.mt-main')

@section('page-title') <?= $pageTitle; ?> @endsection
@section('page-sub-title') <?= $pageSubTitle; ?> @endsection

@section('content')

    <div class="card card-custom">
        <div class="row border-bottom mb-7">

            <div class="col-md-6">
                <div class="card card-custom">
                    <form name="searchorder" action="{{ url('/driver/find-order') }}" method="POST">
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

    </div>

    <div class="card card-custom">

        <div class="row border-bottom mb-7">
            <div class="col-md-12">

                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Order Filter</h3>
                        </div>
                        <div class="card-toolbar">

                        </div>
                    </div>

                    <div class="card-body p-0 mb-7">

                        <div class="row border-bottom mb-7">

                            <div class="col-md-12">

                                <form name="filter_driver_order_form" id="filter_driver_order_form" action="{{ url('/driver/filter-order') }}" method="POST">
                                    @csrf

                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <select class="form-control" id="emirates_region" name="emirates_region" >
                                                <option value="" >Select a Region</option>
                                                @foreach($emirates as $emirateKey => $emirateName)
                                                    <option value="{{ $emirateKey }}" >{{ $emirateName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <select class="form-control" id="channel_filter" name="channel_filter" >
                                                <option value="" >Select a Channel</option>
                                                @foreach($availableApiChannels as $channelKey => $channelEl)
                                                    <option value="{{ $channelEl['id'] }}" >{{ $channelEl['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <select class="form-control" id="order_status_filter" name="order_status_filter" >
                                                <option value="" >Select an Order Status</option>
                                                @foreach($availableStatuses as $statusKey => $statusEl)
                                                    <option value="{{ $statusKey }}" >{{ $statusEl }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="delivery_date_filter" name="delivery_date_filter" readonly placeholder="Select Delivery Date"/>
                                        </div>
                                        <div class="col-lg-4">
                                            <select class="form-control" id="delivery_slot_filter" name="delivery_slot_filter" >
                                                <option value="" >Select a Time Slot</option>
                                                @foreach($deliveryTimeSlots as $deliveryEl)
                                                    <option value="{{ $deliveryEl }}" >{{ $deliveryEl }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 text-right">
                                            <button type="button" id="filter_driver_order_filter_btn" class="btn btn-primary btn-lg mr-2">Filter</button>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>

                        <div class="row border-bottom mb-7">

                            <div class="col-md-12">

                                <div class="table-responsive text-center" id="driver_order_filter_table_area">
                                    <table class="table table-bordered" id="driver_order_filter_table">

                                        <thead>
                                            <tr>
                                                <th># Order Id</th>
                                                <th>Channel</th>
                                                <th>Emirates</th>
                                                <th>Delivery Date</th>
                                                <th>Delivery Schedule Interval</th>
                                                <th>Picked At</th>
                                                <th>Delivered At</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        <?php foreach($driverOrders as $record)
                                        {

                                            $orderId = $record->id;
                                            $incrementId = $record->increment_id;
                                            $apiChannelId = $record->channel;
                                            $apiChannel = $availableApiChannels[$apiChannelId];
                                            $emirateId = $record->region_code;
                                            $emirateName = $emirates[$emirateId];
                                            $deliveryDate = $record->delivery_date;
                                            $deliverySlot = $record->delivery_time_slot;
                                            $orderStatusId = $record->order_status;
                                            $orderStatus = $availableStatuses[$orderStatusId];
                                            $deliveryPickerTime = '';
                                            $deliveryDriverTime = '';
                                            $deliveryPickerData = $record->pickupData;
                                            $deliveryDriverData = $record->deliveryData;
                                            $viewLink = url('/driver/order-view/' . $orderId);
                                            if ($deliveryPickerData && (count($deliveryPickerData) > 0)) {
                                                $pickerDetail = $deliveryPickerData[0];
                                                $deliveryPickerTime = $serviceHelper->getFormattedTime($pickerDetail->done_at, 'F d, Y, h:i:s A');
                                            }
                                            if ($deliveryDriverData && (count($deliveryDriverData) > 0)) {
                                                $driverDetail = $deliveryDriverData[0];
                                                $deliveryDriverTime = $serviceHelper->getFormattedTime($driverDetail->done_at, 'F d, Y, h:i:s A');
                                            }

                                        ?>
                                        <tr>
                                            <td>{{ $incrementId }}</td>
                                            <td>{{ $apiChannel['name'] }}</td>
                                            <td>{{ $emirateName }}</td>
                                            <td>{{ $deliveryDate }}</td>
                                            <td>{{ $deliverySlot }}</td>
                                            <td>{{ $deliveryPickerTime }}</td>
                                            <td>{{ $deliveryDriverTime }}</td>
                                            <td>
                                                <span class="label label-lg font-weight-bold label-light-primary label-inline">
                                                    {{ $orderStatus }}
                                                </span>
                                            </td>
                                            <td><a href="{{ $viewLink }}" target="_blank">View Order</a></td>
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

                </div>
            </div>

        </div>

    </div>

@endsection

@section('custom-js-section')

    <script src="{{ asset('js/driver.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            DriverCustomJsBlocks.dashboardPage('{{ url('/') }}');
        });
    </script>

@endsection
