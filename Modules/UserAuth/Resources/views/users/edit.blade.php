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

                    <!--begin::Card Title-->
                    <div class="card-title">
                        <h3 class="card-label">
                            <?= $pageSubTitle; ?>
                        </h3>
                    </div>
                    <!--end::Card Title-->

                    <!--begin::Card Toolbar-->
                    <div class="card-toolbar">


                    </div>
                    <!--end::Card Toolbar-->

                </div>
                <!--end::Card Header-->

                <!--begin::Form-->
                <form class="form" id="user_edit_form" action="{{ url('/userauth/users/update/' . $givenUserData->id) }}" method="post">

                    @csrf

                    <!--begin::Card Body-->
                    <div class="card-body">

                        <div class="form-group row mt-4">
                            <label  class="col-3 col-form-label text-right">E-Mail</label>
                            <label  class="col-6 col-form-label text-left">{{ $givenUserData->email }}</label>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">Name<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter User Name" value="{{ $givenUserData->name }}"/>
                                <span class="form-text text-muted">Please enter the name of the user.</span>
                            </div>
                        </div>

                        @if(\Modules\UserRole\Http\Middleware\AuthUserPermissionResolver::permitted('user-roles.assign'))
                            @if(!$givenUserData->isDefaultUser())
                            <div class="form-group row mb-1">
                                <label  class="col-3 col-form-label text-right">Role</label>
                                <?php
                                    $mappedUserRole = null;
                                    if ($givenUserData->mappedRole && (count($givenUserData->mappedRole) > 0)) {
                                        $mappedUserRole = $givenUserData->mappedRole[0];
                                    }
                                ?>
                                <div class="col-6">
                                    <select class="form-control" id="user_role" name="user_role" >
                                        <option value="" {{ (is_null($mappedUserRole)) ? "selected" : "" }}>Not Assigned</option>
                                        @foreach($userRoles as $userRoleEl)
                                            <option value="{{ $userRoleEl->id }}" {{ (!is_null($mappedUserRole) && ($mappedUserRole->id == $userRoleEl->id)) ? "selected" : "" }}>
                                                {{ $userRoleEl->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        @endif

                    </div>
                    <!--end::Card Body-->

                    <!--begin::Card Footer-->
                    <div class="card-footer text-right">
                        <button type="submit" id="edit_user_submit_btn" class="btn btn-primary font-weight-bold mr-2">
                            <i class="la la-save"></i>Save User
                        </button>
                        <button type="button" id="edit_user_cancel_btn" class="btn btn-light-primary font-weight-bold">Cancel</button>
                    </div>
                    <!--end::Card Footer-->

                </form>
                <!--end::Form-->

            </div>
            <!--end::Card-->

        </div>
    </div>

@endsection

@section('custom-js-section')

    <script src="{{ asset('js/users.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            UsersCustomJsBlocks.editPage('{{ url('/') }}');
        });
    </script>

@endsection
