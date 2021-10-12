<?php

namespace Modules\UserAuth\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Redirect;
use Auth;
use Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Modules\UserRole\Entities\UserRole;
use Modules\UserRole\Entities\UserRoleMap;

class UserAuthController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectRoute = 'dashboard.index';


    /**
     * UserAuthController constructor.
     */
    public function __construct()
    {

    }

    /**
     * Show the Login Page for the System.
     * @return Renderable
     */
    public function index()
    {
        return redirect()->route('userauth.login');
    }

    /**
     * Show the Login Page for the System.
     * @return Renderable
     */
    public function login()
    {
        return view('userauth::login-form');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request) {

        $validator = Validator::make($request->all() , [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'EMail Address should be provided.',
            'email.email' => 'EMail Address should be valid.',
            'password.required' => 'Password should be provided.',
            'password.min' => 'Password should be minimum :min characters.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        if (Auth::guard('auth-user')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->get('remember'))) {

            $authUserData = Auth::guard('auth-user')->user()->toArray();
            $userDetails = [
                'id' => $authUserData['id'],
                'name' => $authUserData['name'],
                'email' => $authUserData['email'],
                'roleId' => null,
                'roleCode' => null,
            ];
            $roleMapData = UserRoleMap::firstWhere('user_id', $authUserData['id']);
            if ($roleMapData) {
                $mappedRoleId = $roleMapData->role_id;
                $roleData = UserRole::find($mappedRoleId);
                if ($roleData) {
                    $userDetails['roleId'] = $roleData->id;
                    $userDetails['roleCode'] = $roleData->code;
                }
            }
            $request->session()->put('authUserData', $userDetails);

            return redirect()->intended(route($this->redirectRoute));
        }

        return back()
            ->with('error', 'The user sign-in failed!')
            ->withInput($request->only('email', 'remember'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logout(Request $request)
    {
        Auth::guard('auth-user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('userauth.login');
    }

}
