<div class="aside aside-left d-flex flex-column" id="kt_aside">

    <!--begin::Brand-->
    <div class="aside-brand d-flex flex-column align-items-center flex-column-auto py-4 py-lg-8">
        <!--begin::Logo-->
        <a href="{{ url('/')  }}">
            <img alt="Logo" src="{{ asset('ktmt/media/logos/g_b_logo.png') }}" class="max-h-30px" />
        </a>
        <!--end::Logo-->
    </div>
    <!--end::Brand-->

    <!--begin::Nav Wrapper-->
    <div class="aside-nav d-flex flex-column align-items-center flex-column-fluid pt-7">

        <!--begin::Nav-->
        <ul class="nav flex-column">

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Dashboard">
                <a href="{{ route('dashboard.index') }}" class="nav-link btn btn-icon btn-clean btn-icon-white btn-lg <?php if($pageTitle=='dashboard'){?> active <?php } ?>">
                    <i class="flaticon2-protection icon-lg"></i>
                </a>
            </li>
            <!--end::Item-->

            <?php /* ?>

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Update Stock">
                <a href="{{ route('stock.update') }}" class="nav-link btn btn-icon btn-icon-white btn-lg <?php if($pageTitle=='updatestock'){?>btn-clean active <?php } ?>">
                    <img src="{{ asset('ktmt/media/update-stock.png') }}"/>
                </a>
            </li>
            <!--end::Item-->

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Out of Stock Report">
                <a href="{{ route('stock.oos-report') }}" class="nav-link btn btn-icon btn-icon-white btn-lg <?php if($pageTitle=='outofstock'){?>btn-clean active <?php } ?>">
                    <img src="{{ asset('ktmt/media/out-of-stock-report.png') }}"/>
                </a>
            </li>
            <!--end::Item-->

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Order Items Sales Report">
                <a href="{{ route('sales.order-items-report') }}" class="nav-link btn btn-icon btn-icon-white btn-lg <?php if($pageTitle=='salesreport'){?>btn-clean active <?php } ?>">
                    <img src="{{ asset('ktmt/media/order-items-sales-report.png') }}"/>
                </a>
            </li>
            <!--end::Item-->

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="POS System">
                <a href="{{ route('sales.pos') }}" class="nav-link btn btn-icon btn-icon-white btn-lg <?php if($pageTitle=='search'){?>btn-clean active <?php } ?>">
                    <img src="{{ asset('ktmt/media/pos_icon.png') }}"/>
                </a>
            </li>
            <!--end::Item-->

            <!--begin::Item-->
            <li class="nav-item mb-5" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="InStores Sales Report">
                <a href="{{ route('sales.instore-report') }}" class="nav-link btn btn-icon btn-icon-white btn-lg <?php if($pageTitle=='salesreport'){?>btn-clean active <?php } ?>">
                    <img src="{{ asset('ktmt/media/order-items-sales-report.png') }}"/>
                </a>
            </li>
            <!--end::Item-->

            <?php */ ?>

        </ul>
        <!--end::Nav-->

    </div>
    <!--end::Nav Wrapper-->

</div>
