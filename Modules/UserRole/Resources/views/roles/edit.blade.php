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
                <form class="form" id="user_role_edit_form" action="{{ url('/userrole/roles/update/' . $givenUserRole->id) }}" method="post">

                    @csrf

                    <!--begin::Card Body-->
                    <div class="card-body">

                        <div class="form-group row mt-4">
                            <label  class="col-3 col-form-label text-right">Code</label>
                            <label  class="col-6 col-form-label text-left">{{ $givenUserRole->code }}</label>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">Name</label>
                            <div class="col-6">
                                <input type="text" class="form-control" id="role_name" name="role_name" placeholder="Enter Role Display Name" value="{{ $givenUserRole->display_name }}"/>
                                <span class="form-text text-muted">This will be displayed to show the Role.</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label  class="col-3 col-form-label text-right">Description</label>
                            <div class="col-6">
                                <textarea class="form-control" id="role_desc" name="role_desc" rows="3">{{ $givenUserRole->description }}</textarea>
                                <span class="form-text text-muted">A short description about the Role.</span>
                            </div>
                        </div>

                        <div class="form-group row mb-1">
                            <label  class="col-3 col-form-label text-right">Active<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select class="form-control" id="role_active" name="role_active" >
                                    <option value="1" {{ ($givenUserRole->is_active == 1) ? "selected" : "" }}>Active</option>
                                    <option value="0" {{ ($givenUserRole->is_active == 0) ? "selected" : "" }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <!--end::Card Body-->

                    <!--begin::Card Footer-->
                    <div class="card-footer text-right">
                        <button type="submit" id="edit_user_role_submit_btn" class="btn btn-primary font-weight-bold mr-2">
                            <i class="la la-save"></i>Save Role
                        </button>
                        <button type="button" id="edit_user_role_cancel_btn" class="btn btn-light-primary font-weight-bold">Cancel</button>
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

    <script src="{{ asset('js/userrole.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            UserRolesCustomJsBlocks.editPage('{{ url('/') }}');
        });
    </script>

@endsection
