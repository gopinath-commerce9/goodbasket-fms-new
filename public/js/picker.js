"use strict";
var PickerCustomJsBlocks = function() {

    var setDeliveryDateFilterDatePicker = function() {
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }
        $('#delivery_date_filter').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows
        });
    };

    var initPickerSaleOrderTable = function() {

        var table = $('#picker_order_filter_table');

        var dataTable = table.DataTable({
            responsive: true,
            dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [],
        });

        return dataTable;

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
            setDeliveryDateFilterDatePicker();
            var dataTable  = initPickerSaleOrderTable();
            jQuery(document).ready(function() {
                $('button#filter_picker_order_filter_btn').on('click', function(e){
                    var targetForm = $('form#filter_picker_order_form');
                    $.ajax({
                        url: targetForm.attr('action'),
                        method: targetForm.attr('method'),
                        data: targetForm.serialize(),
                        beforeSend: function() {
                            KTApp.block('#picker_order_filter_table_area', {
                                overlayColor: '#000000',
                                state: 'danger',
                                message: 'Please wait...'
                            });
                        },
                        success: function(response){
                            if (response.length > 0) {
                                var tableHtml = '';
                                $.each(response, function(index,value) {
                                    tableHtml += '<tr></tr><td>' + value.increment_id + '</td>';
                                    tableHtml += '<td>' + value.channel + '</td>';
                                    tableHtml += '<td>' + value.region + '</td>';
                                    tableHtml += '<td>' + value.delivery_date + '</td>';
                                    tableHtml += '<td>' + value.delivery_time_slot + '</td>';
                                    tableHtml += '<td>' + value.delivery_picker_time + '</td>';
                                    tableHtml += '<td><span class="label label-lg font-weight-bold label-light-primary label-inline">' + value.order_status + '</span></td>';
                                    tableHtml += '<td><a href="' + value.view_link + '" target="_blank">View Order</a></td></tr>';
                                });
                                $('table#picker_order_filter_table tbody').html(tableHtml);
                            } else {
                                $('table#picker_order_filter_table tbody').html('<td colspan="8" class="text-center">No Orders found!</td>');
                            }
                            if($.fn.dataTable.isDataTable('#picker_order_filter_table')) {
                                $('#picker_order_filter_table').dataTable({
                                    retrieve: true,
                                    paging: true
                                });
                            }
                            KTApp.unblock('#picker_order_filter_table_area');
                        }
                    });

                });
            });
        },
        orderViewPage: function(hostUrl) {

        },
    };

}();
