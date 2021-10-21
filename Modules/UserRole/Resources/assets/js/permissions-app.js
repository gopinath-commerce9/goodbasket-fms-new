"use strict";
var UserPermissionsCustomJsBlocks = function() {

    var initUserRoleTable = function() {

        var table = $('#user_permissions_table');

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

    var initUserRoleUserTable = function() {

        var table = $('#role_user_list_table');

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

    return {
        listPage: function() {
            initUserRoleTable();
        },
        viewPage: function() {
            initUserRoleUserTable();
        },
        newPage: function(hostUrl) {
            jQuery('button#new_user_role_cancel_btn').on('click', function(e) {
                window.location = hostUrl + '/userrole/roles';
            });
        },
        editPage: function(hostUrl) {
            jQuery('button#edit_user_role_cancel_btn').on('click', function(e) {
                window.location = hostUrl + '/userrole/roles';
            });
        }
    };

}();
