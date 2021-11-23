"use strict";
var SalesCustomJsBlocks = function() {

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

    $.fn.dataTable.Api.register('column().title()', function() {
        return $(this.header()).text().trim();
    });

    var initSaleOrderTable = function() {

        var table = $('#sales_order_filter_table');
        var targetForm = $('form#filter_sales_order_form');
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

        $('button#filter_sales_order_filter_btn').on('click', function(e) {
            e.preventDefault();
            dataTable.table().draw();
        });

        $('button#filter_sales_order_reset_btn').on('click', function(e) {
            e.preventDefault();
            $('.datatable-input').each(function() {
                $(this).val('');
            });
            dataTable.table().draw();
        });

    };

    var posHideOnlinePayment = function (orderSource) {
        if(orderSource == 'ELGROCER') {
            $("#paymentMethod").append(new Option("Online Payment Method", "saleschannel"));
        } else {
            $("#paymentMethod option[value='saleschannel']").remove();
        }
        if(orderSource == 'INSTORE') {
            $('#order_source_id_div').hide();
            posFillForm();
        } else {
            $('#order_source_id_div').show();
            $("form#order-form")[0].reset();
            $('#channel-Id').val("ELGROCER");
            $('.customer_info').show();
            $('#email').val("elgrocer@goodbasket.com");
        }
    };

    var posApplyServiceCharge = function (source, sourceList) {
        if(source) {
            var sourcedtls =  sourceList[source];
            $.each(sourcedtls, function(key, value){
                if(key == 'charge') {
                    $('#service_charge').val(value);
                    $('#sc-span').html('AED'+value);
                    posCalculateTotal();
                }
            });
        } else {
            $('#service_charge').val('0.00');
            $('#sc-span').html('AED0.00');
            posCalculateTotal();
        }
    };

    var posFetchAreas = function (emirate, areaList) {
        let dropdown = $('#city');
        dropdown.empty();
        dropdown.append('<option selected="true" disabled>Choose Area</option>');
        dropdown.prop('selectedIndex', 0);
        var areas = areaList[emirate]
        $.each(areas, function(key, value){
            dropdown.append($('<option></option>').attr('value', value.area_code).text(value.area_name));
        });
        $('select[name=city] option:eq(1)').attr('selected', 'selected');
    };

    var posClearCart = function () {
        $('#cart-item').html('<div class="example-preview mb-5"><div class="spinner spinner-track spinner-success mr-15">&nbsp;</div></div>');
        var targetForm = $('form#barcode-form');
        var formDataArray = targetForm.serializeArray();
        var tokenValue = '';
        jQuery.each(formDataArray, function(i, field){
            if (field.name == '_token') {
                tokenValue = field.value;
            }
        });
        $.ajax({
            url: targetForm.attr('action'),
            type: targetForm.attr('method'),
            data: { action:'clearcart', _token: tokenValue },
            success: function (response) {
                $('#spinner').css('display','none');
                $('#cart-item').html(response.html);
                //calculateTotal();
                $('#subtotal-span').html('AED0.00');
                $('#discount-span').html('AED0.00');
                $('#sc-span').html('AED0.00');
                $('#total-span').html('<strong>AED0.00</strong>');
                $('#create_btn').prop('disabled', true);
            }
        });
    };

    var posFillForm = function () {
        $('.customer_info').hide();
        $('#firstname').val("InStore");
        $('#lastname').val("InStore");
        $('#email').val("instore@goodbasket.com");
        $('#telephone').val("+97155555555");
        $('#street').val("In Store");
        $('#delivery_time_slot').val("10:00 AM - 2:00 PM");
    };

    var posCalculateTotal = function () {
        //var disamt = $('#discount').val();
        $('#discount-span').html('AED' + $('#discount').val());
        if($('#subtotal').val() != undefined) {
            var subTotal = parseFloat($('#subtotal').val());
            var serviceCharge = parseFloat($('#service_charge').val());
            var totalAmount = subTotal + serviceCharge;
            if($('#discount').val() && $('#discount').val() != 0) {
                totalAmount = totalAmount - parseFloat($('#discount').val());
            } else {
                $('#discount-span').html('AED0.00');
            }
            $('#subtotal-span').html('AED' + subTotal.toFixed(2));
            $('#total-span').html('<strong>AED' + totalAmount.toFixed(2)+'</strong>');
            if($('#subtotal').val() <= 0) {
                $('#create_btn').prop('disabled', true);
            } else {
                $('#create_btn').prop('disabled', false);
            }
        } else {
            $('#discount-span').html('AED0.00');
        }
    };

    var posRemoveItem = function (item) {
        $('#cart-item').html('<div class="example-preview mb-5"><div class="spinner spinner-track spinner-success mr-15">&nbsp;</div></div>');
        var targetForm = $('form#barcode-form');
        var formDataArray = targetForm.serializeArray();
        var tokenValue = '';
        jQuery.each(formDataArray, function(i, field){
            if (field.name == '_token') {
                tokenValue = field.value;
            }
        });
        $.ajax({
            url: targetForm.attr('action'),
            type: targetForm.attr('method'),
            data : { item: item, action: 'removeitem', _token: tokenValue },
            success : function(response) {
                $('#cart-item').html(response.html);
                //$('#subtotal-span').html('AED'+$('#subtotal').val());
                posCalculateTotal();
            }
        });
    };

    var posReduceProduct = function (id, row) {
        $('#quan_td_'+row).html('<div class="input-group-sm input-group"><div class="spinner spinner-track spinner-primary spinner-sm mr-15"></div></div>');
        //$('#cart-item').html('<div class="example-preview mb-5"><div class="spinner spinner-track spinner-success mr-15">&nbsp;</div></div>');
        var targetForm = $('form#barcode-form');
        var formDataArray = targetForm.serializeArray();
        var tokenValue = '';
        jQuery.each(formDataArray, function(i, field){
            if (field.name == '_token') {
                tokenValue = field.value;
            }
        });
        $.ajax({
            url: targetForm.attr('action'),
            type: targetForm.attr('method'),
            data : { id: id, row: row, action: 'remove', _token: tokenValue },
            success : function(response) {
                $('#cart-item').html(response.html);
                posCalculateTotal();
            }
        });
    };

    var posAddProduct = function (id, row) {
        $('#quan_td_'+row).html('<div class="input-group-sm input-group"><div class="spinner spinner-track spinner-primary spinner-sm mr-15"></div></div>');
        //$('#cart-item').html('<div class="example-preview mb-5"><div class="spinner spinner-track spinner-success mr-15">&nbsp;</div></div>');
        var targetForm = $('form#barcode-form');
        var formDataArray = targetForm.serializeArray();
        var tokenValue = '';
        jQuery.each(formDataArray, function(i, field){
            if (field.name == '_token') {
                tokenValue = field.value;
            }
        });
        $.ajax({
            url: targetForm.attr('action'),
            type: targetForm.attr('method'),
            data : { id: id, row: row, action: 'add', _token: tokenValue },
            success : function(response) {
                $('#cart-item').html(response.html);
                posCalculateTotal();
            }
        });
    };

    var initOosReportTable = function() {

        var table = $('#oos_report_table');

        table.DataTable({
            responsive: true,
            dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [],
        });

    };

    var initFilterOrderItemDateRangePicker = function () {
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
        listPage: function(hostUrl){
            setDeliveryDateFilterDatePicker();
            initSaleOrderTable();
        },
        posPage: function(hostUrl, areaList, sourceList){

            $('select#channel-Id').on('change', function (e) {
                var currentSource = $(this).val();
                posHideOnlinePayment(currentSource);
                posApplyServiceCharge(currentSource, sourceList);
            });

            $('select#region').on('change', function (e) {
                var currentRegion = $(this).val();
                posFetchAreas(currentRegion, areaList);
            });
            $("#region option:contains('Dubai')").attr('selected', 'selected').trigger('change');

            $('#barcode').keyup(function(){
                var barCode = $(this).val();
                if(barCode.length === 13){
                    $('#cart-btn').click();
                }
            });

            $("#cart-btn").on('click', function (e) {
                $('#cart-btn').prop('disabled', true);
                var btn = KTUtil.getById("cart-btn");
                KTUtil.btnWait(btn, "spinner spinner-left spinner-white pl-15 disabled", "Adding...");
                //$('#cart-item').html('<div class="example-preview mb-5"><div class="spinner spinner-track spinner-success mr-15">&nbsp;</div></div>');
                e.preventDefault();
                var targetForm = $('form#barcode-form');
                $.ajax({
                    url: targetForm.attr('action'),
                    type: targetForm.attr('method'),
                    data: targetForm.serialize(),
                    success: function (response) {
                        KTUtil.btnRelease(btn);
                        $('#cart-btn').prop('disabled', false);
                        $('#cart-btn').html("<i class='flaticon-shopping-basket icon-nm'></i> Add to Cart");
                        $('#barcode').val('');
                        $('#cart-item').html(response.html);
                        //$('#subtotal-span').html('AED'+$('#subtotal').val());
                        posCalculateTotal();
                    }
                });

            });

            $('a#clear-cart-btn').on('click', function (e) {
                e.preventDefault();
                posClearCart();
            });

            $(document).on('click', 'a.item-remove-btn', function (e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                posRemoveItem(productId);
            });

            $(document).on('click', 'a.product-remove-btn', function (e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var productRow = $(this).data('row-index');
                posReduceProduct(productId, productRow);
            });

            $(document).on('click', 'a.product-add-btn', function (e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var productRow = $(this).data('row-index');
                posAddProduct(productId, productRow);
            });

            $('button#create_btn').on('click', function (e) {
                var targetForm = $('form#order-form');
                $('#create_btn').prop('disabled', true);
                var btn = KTUtil.getById("create_btn");
                KTUtil.btnWait(btn, "spinner spinner-left spinner-white pl-15 disabled", "Processing...");
                e.preventDefault();
                $.ajax({
                    url: targetForm.attr('action'),
                    type: targetForm.attr('method'),
                    data: targetForm.serialize(),
                    dataType: "json",
                    success : function(response) {
                        //$('#cart-btn').prop('disabled', true);
                        KTUtil.btnRelease(btn);
                        if(response.success === true){
                            swal.fire({
                                html: response.html,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok!",
                                customClass: {
                                    confirmButton: "btn font-weight-bold btn-light-primary"
                                }
                            }).then(function() {
                                KTUtil.scrollTop();
                                targetForm[0].reset();
                                targetForm[0].reset();
                                posClearCart();
                            });
                        } else {
                            swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn font-weight-bold btn-light-primary"
                                }
                            }).then(function() {
                                KTUtil.scrollTop();
                            });
                        }
                    }
                });
            });

        },
        updateStockPage: function(hostUrl) {

        },
        oosReportPage: function(hostUrl) {
            initOosReportTable();
        },
        itemsReportPage: function(hostUrl) {
            initFilterOrderItemDateRangePicker();
        },
    };

}();
