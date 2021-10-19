"use strict";
var UserRolesCustomJsBlocks = function() {

    var initUserRoleTable = function() {

        var table = $('#user_role_table');

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
        init: function() {
            initUserRoleTable();
        }
    };

}();

jQuery(document).ready(function() {
    UserRolesCustomJsBlocks.init();
});


