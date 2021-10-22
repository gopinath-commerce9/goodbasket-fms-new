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
                <form class="form" id="user_new_form" action="{{ url('/userauth/users/store') }}" method="post">

                    @csrf

                    <!--begin::Card Body-->
                    <div class="card-body">

                        <div class="form-group row mt-4">
                            <label  class="col-3 col-form-label text-right">Name<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input type="text" class="form-control" id="user_name" name="user_name" value="{{ old('user_name') }}" required placeholder="Enter User Name"/>
                                <span class="form-text text-muted">Please enter the name of the user.</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">E-Mail<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input type="email" class="form-control" id="user_email" name="user_email" value="{{ old('user_email') }}" placeholder="Enter User E-Mail"/>
                                <span class="form-text text-muted">Please enter the user e-mail.</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">Password<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <div class="input-group show-hide-password-group" id="user_password_form_group">
                                    <input type="password" class="form-control" id="user_password" name="user_password" value="{{ old('user_password') }}" placeholder="Enter Password"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text show-hide-password"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                                <span class="form-text text-muted">
                                    Please enter the password. The password must be more than 8 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">Confirm Password<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <div class="input-group show-hide-password-group" id="user_password_conf_form_group">
                                    <input type="password" class="form-control" id="user_password_confirmation" name="user_password_confirmation"
                                           value="{{ old('user_password_confirmation') }}" placeholder="Confirm Password"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text show-hide-password"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                                <span class="form-text text-muted">Please confirm the password.</span>
                            </div>
                        </div>

                    </div>
                    <!--end::Card Body-->

                    <!--begin::Card Footer-->
                    <div class="card-footer text-right">
                        <button type="submit" id="new_user_submit_btn" class="btn btn-primary font-weight-bold mr-2">
                            <i class="la la-plus"></i>Add User
                        </button>
                        <button type="button" id="new_user_cancel_btn" class="btn btn-light-primary font-weight-bold">Cancel</button>
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
            UsersCustomJsBlocks.newPage('{{ url('/') }}');
        });
    </script>

@endsection
