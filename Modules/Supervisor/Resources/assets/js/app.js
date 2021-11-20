"use strict";
var SupervisorCustomJsBlocks = function() {

    var initFilterDeliveryDateRangePicker = function () {
        var filterDRPicker = $('#delivery_date_range_filter').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary'
        }, function(start, end, label) {
            $('input#delivery_date_start_filter').val(start.format('YYYY-MM-DD'));
            $('input#delivery_date_end_filter').val(end.format('YYYY-MM-DD'));
        });
        filterDRPicker.on('show.daterangepicker', function(ev, picker) {
            //do something, like clearing an input
            $('input#delivery_date_start_filter').val(picker.startDate.format('YYYY-MM-DD'));
            $('input#delivery_date_end_filter').val(picker.startDate.format('YYYY-MM-DD'));
        });
    };

    var saleOrderSalesBarChartSetter = function () {
        var chart = new ApexCharts(document.querySelector("#sale_orders_sales_bar_chart"), {
            series: [],
            chart: {
                type: 'bar',
                height: 400,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '50%'
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: 'Sale Order Sales'
            },
            xaxis: {
                title: {
                    text: 'Sale Order Total Amount'
                },
                categories: [],
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Delivery Date(s)'
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            },
            noData: {
                text: 'No Data Found!'
            }
        });
        chart.render();
        return chart;
    };

    var saleOrderStatusBarChartSetter = function () {
        var chart = new ApexCharts(document.querySelector("#sale_orders_status_bar_chart"), {
            series: [],
            chart: {
                type: 'bar',
                height: 400,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '50%'
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: 'Sale Order Status'
            },
            xaxis: {
                title: {
                    text: 'Number Of Orders'
                },
                categories: [],
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Delivery Date(s)'
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Order(s)"
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            },
            noData: {
                text: 'No Data Found!'
            }
        });
        chart.render();
        return chart;
    };

    var getSalesChartData = function (chartObj) {
        var targetForm = $('#filter_supervisor_order_form');
        var formData = targetForm.serializeArray();
        formData.push({name: 'action', value: 'sales_chart'});
        $.ajax({
            url: targetForm.attr('action'),
            method: targetForm.attr('method'),
            data: formData,
            beforeSend: function() {
                KTApp.block('#sale_order_sales_chart_card_row', {
                    overlayColor: '#000000',
                    state: 'danger',
                    message: 'Please wait...'
                });
            },
            success: function(data){
                KTApp.unblock('#sale_order_sales_chart_card_row');
                chartObj.updateOptions({
                    series: data.series,
                    xaxis: {
                        categories: data.xaxis
                    }
                });
            }
        });
    };

    var getStatusChartData = function (chartObj) {
        var targetForm = $('#filter_supervisor_order_form');
        var formData = targetForm.serializeArray();
        formData.push({name: 'action', value: 'status_chart'});
        $.ajax({
            url: targetForm.attr('action'),
            method: targetForm.attr('method'),
            data: formData,
            beforeSend: function() {
                KTApp.block('#sale_order_status_chart_card_row', {
                    overlayColor: '#000000',
                    state: 'danger',
                    message: 'Please wait...'
                });
            },
            success: function(data){
                KTApp.unblock('#sale_order_status_chart_card_row');
                chartObj.updateOptions({
                    series: data.series,
                    xaxis: {
                        categories: data.xaxis
                    }
                });
            }
        });
    };

    $.fn.dataTable.Api.register('column().title()', function() {
        return $(this.header()).text().trim();
    });

    var initSupervisorSaleOrderTable = function(saleOrderSalesChart, saleOrderStatusChart) {

        var table = $('#supervisor_order_filter_table');
        var targetForm = $('form#filter_supervisor_order_form');
        var dataTable = table.DataTable({
            responsive: true,
            dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 5,
            order: [[0, 'asc']],
            searchDelay: 500,
            processing: true,
            language: {
                processing: '<div class="btn btn-secondary spinner spinner-dark spinner-right">Please Wait</div>',
            },
            serverSide: true,
            ajax: {
                url: targetForm.attr('action'),
                type: targetForm.attr('method'),
                data: function(d) {
                    $.each(targetForm.serializeArray(), function(key, val) {
                        d[val.name] = val.value;
                    });
                    d['columnsDef'] = [
                        'incrementId', 'channel', 'region', 'customerName', 'deliveryDate', 'deliveryTimeSlot', 'deliveryPicker',
                        'deliveryPickerTime', 'deliveryDriver', 'deliveryDriverTime', 'orderStatus', 'actions'
                    ];
                },
            },
            columns: [
                {data: 'incrementId'},
                {data: 'channel'},
                {data: 'region'},
                {data: 'customerName'},
                {data: 'deliveryDate'},
                {data: 'deliveryTimeSlot'},
                {data: 'deliveryPicker'},
                {data: 'deliveryPickerTime'},
                {data: 'deliveryDriver'},
                {data: 'deliveryDriverTime'},
                {data: 'orderStatus'},
                {data: 'actions', responsivePriority: -1},
            ],
            columnDefs: [{
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function(data, type, full, meta) {
                    return '<a href="' + data + '" target="_blank">View Order</a>';
                },
            }, {
                targets: 10,
                title: 'Status',
                orderable: true,
                render: function(data, type, full, meta) {
                    return '<span class="label label-lg font-weight-bold label-light-primary label-inline">' + data + '</span>';
                },
            }],
        });

        $('button#filter_supervisor_order_filter_btn').on('click', function(e) {
            e.preventDefault();
            getSalesChartData(saleOrderSalesChart);
            getStatusChartData(saleOrderStatusChart);
            dataTable.table().draw();
        });

        $('button#filter_supervisor_order_reset_btn').on('click', function(e) {
            e.preventDefault();
            $('.datatable-input').each(function() {
                $(this).val('');
            });
            $('.datatable-date-input').each(function() {
                var d = new Date();
                var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
                $(this).val(strDate);
            });
            getSalesChartData(saleOrderSalesChart);
            getStatusChartData(saleOrderStatusChart);
            dataTable.table().draw();
        });

    };

    var showAlertMessage = function(message) {
        $("div.custom_alert_trigger_messages_area")
            .html('<div class="alert alert-custom alert-dark alert-light-dark fade show" role="alert">' +
                '<div class="alert-icon"><i class="flaticon-information"></i></div>' +
                '<div class="alert-text">' + message + '</div>' +
                '<div class="alert-close">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true"><i class="ki ki-close"></i></span>' +
                '</button>' +
                '</div>' +
                '</div>');
    };

    return {
        dashboardPage: function(hostUrl){
            initFilterDeliveryDateRangePicker();
            var saleOrderSalesChart = saleOrderSalesBarChartSetter();
            var saleOrderStatusChart = saleOrderStatusBarChartSetter();
            getSalesChartData(saleOrderSalesChart);
            getStatusChartData(saleOrderStatusChart);
            initSupervisorSaleOrderTable(saleOrderSalesChart, saleOrderStatusChart);
        },
        orderViewPage: function(hostUrl) {

        },
    };

}();
