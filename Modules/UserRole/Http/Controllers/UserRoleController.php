<?php

namespace Modules\UserRole\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\UserRole\Entities\UserRole;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'User Roles';

        $userRoleList = UserRole::all();

        $userRolesTotal = $userRoleList->count();

        return view('userrole::roles.list', compact(
            'pageTitle',
            'pageSubTitle',
            'userRoleList',
            'userRolesTotal'
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
     * @param int $roleId
     * @return Renderable
     */
    public function show($roleId)
    {
        return view('userrole::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $roleId
     * @return Renderable
     */
    public function edit($roleId)
    {
        return view('userrole::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $roleId
     * @return Renderable
     */
    public function update(Request $request, $roleId)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $roleId
     * @return Renderable
     */
    public function destroy($roleId)
    {
        //
    }
}
