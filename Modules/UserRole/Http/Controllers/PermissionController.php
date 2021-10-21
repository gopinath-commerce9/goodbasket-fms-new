<?php

namespace Modules\UserRole\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Input;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Modules\UserRole\Entities\PermissionMap;
use Modules\UserRole\Entities\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'User Role Permissions';

        $userPermissionList = Permission::all();

        $userPermissionsTotal = $userPermissionList->count();

        return view('userrole::permissions.list', compact(
            'pageTitle',
            'pageSubTitle',
            'userPermissionList',
            'userPermissionsTotal'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('userrole::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $pId
     * @return Renderable
     */
    public function show($pId)
    {
        return view('userrole::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $pId
     * @return Renderable
     */
    public function edit($pId)
    {
        return view('userrole::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $pId
     * @return Renderable
     */
    public function update(Request $request, $pId)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $pId
     * @return Renderable
     */
    public function destroy($pId)
    {
        //
    }
}
