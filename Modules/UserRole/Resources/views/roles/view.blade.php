@extends('base::layouts.mt-main')

@section('page-title') <?= $pageTitle; ?> @endsection
@section('page-sub-title') <?= $pageSubTitle; ?> @endsection

@section('content')

    <div class="row">
        <div class="col-md-12">

            <!--begin::Card-->
            <div class="card card-custom gutter-b">

                <!--begin::Card Header-->
                <div class="card-header flex-wrap py-3">

                    <!--begin::Card Toolbar-->
                    <div class="card-toolbar">

                        <!--begin::Card Tabs-->
                        <ul class="nav nav-light-info nav-bold nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#user_role_view_info_tab">
                                    <span class="nav-text">Info</span>
                                </a>
                            </li>
                            {{--<li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#user_role_view_users_tab">
                                    <span class="nav-text">Users</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#user_role_view_permissions_tab">
                                    <span class="nav-text">Permissions</span>
                                </a>
                            </li>--}}
                        </ul>
                        <!--end::Card Tabs-->

                    </div>
                    <!--end::Card Toolbar-->

                    <div class="card-toolbar">
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-hover-light-primary btn-icon btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ki ki-bold-more-hor "></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                @if(\Modules\UserRole\Http\Middleware\AuthUserPermissionResolver::permitted('user-roles.update'))
                                    <a href="{{ url('/userrole/roles/edit/' . $givenUserRole->id) }}" class="dropdown-item btn" title="Edit">
                                        Edit
                                    </a>
                                @endif
                                @if(\Modules\UserRole\Http\Middleware\AuthUserPermissionResolver::permitted('user-roles.delete'))
                                    @if(!$givenUserRole->isAdmin())
                                        <a href="{{ url('/userrole/roles/delete/' . $givenUserRole->id) }}" class="dropdown-item btn" title="Delete">
                                            Delete
                                        </a>
                                    @endif
                                @endif
                                <div class="dropdown-divider"></div>
                                <a href="{{ url('/userrole/roles') }}" class="dropdown-item btn">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end::Card Header-->

                <!--begin::Card Body-->
                <div class="card-body">

                    <!--begin::Card Tab Content-->
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="user_role_view_info_tab" role="tabpanel" aria-labelledby="user_role_view_info_tab">

                            <div class="form-group row my-2">
                                <label class="col-2 col-form-label font-size-lg-h2 text-right">Code:</label>
                                <div class="col-10">
                                    <span class="form-control-plaintext font-size-lg-h2 font-weight-bolder text-left">{{ $givenUserRole->code }}</span>
                                </div>
                            </div>

                            <div class="form-group row my-2">
                                <label class="col-2 col-form-label font-size-lg-h2 text-right">Name:</label>
                                <div class="col-10">
                                    <span class="form-control-plaintext font-size-lg-h2 font-weight-bolder text-left">{{ $givenUserRole->display_name }}</span>
                                </div>
                            </div>

                            <div class="form-group row my-2">
                                <label class="col-2 col-form-label font-size-lg-h2 text-right">Description:</label>
                                <div class="col-10">
                                    <span class="form-control-plaintext font-size-lg-h2 font-weight-bolder text-left">{{ $givenUserRole->description }}</span>
                                </div>
                            </div>

                            <div class="form-group row my-2">
                                <label class="col-2 col-form-label font-size-lg-h2 text-right">Active:</label>
                                <div class="col-10">
                                    @if($givenUserRole->is_active == 0)
                                        <span class="label label-lg font-weight-bold label-light-danger label-inline mt-2">No</span>
                                    @elseif($givenUserRole->is_active == 1)
                                        <span class="label label-lg font-weight-bold label-light-success label-inline mt-2">Yes</span>
                                    @else
                                        {{ $givenUserRole->is_active }}
                                    @endif
                                </div>
                            </div>

                        </div>

                        {{--<div class="tab-pane fade" id="user_role_view_users_tab" role="tabpanel" aria-labelledby="user_role_view_users_tab">
                            ...
                        </div>

                        <div class="tab-pane fade" id="user_role_view_permissions_tab" role="tabpanel" aria-labelledby="user_role_view_permissions_tab">
                            ...
                        </div>--}}

                    </div>
                    <!--end::Card Tab Content-->

                </div>
                <!--end::Card Body-->

                <!--begin::Card Footer-->
                <div class="card-footer text-right">
                    <div class="row">
                        <div class="col text-left">
                            <a href="{{ url('/userrole/roles') }}" class="btn btn-outline-primary">
                                <i class="flaticon2-back"></i> Back
                            </a>
                        </div>
                        <div class="col text-right">
                            @if(\Modules\UserRole\Http\Middleware\AuthUserPermissionResolver::permitted('user-roles.update'))
                                <a href="{{ url('/userrole/roles/edit/' . $givenUserRole->id) }}" class="btn btn-warning mr-2" title="Edit">
                                    <i class="flaticon2-pen"></i> Edit
                                </a>
                            @endif
                            @if(\Modules\UserRole\Http\Middleware\AuthUserPermissionResolver::permitted('user-roles.delete'))
                                @if(!$givenUserRole->isAdmin())
                                    <a href="{{ url('/userrole/roles/delete/' . $givenUserRole->id) }}" class="btn btn-danger mr-2" title="Delete">
                                        <i class="flaticon-delete-1"></i> Delete
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Card Footer-->

                </form>
                <!--end::Form-->

            </div>
            <!--end::Card-->

        </div>
    </div>

@endsection
