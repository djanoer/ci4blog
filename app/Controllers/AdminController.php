<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CIAuth;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $helper = ['url', 'form', 'CIMail', 'CIFunctions'];

    public function index()
    {
        $data = [
            'pageTitle' => 'Dashboard',
        ];
        return view('backend/pages/home', $data);
    }

    public function logoutHandler()
    {
        CIAuth::forget();
        return redirect()->route('admin.login.form')->with('fail', 'You are logged out!');
    }

    public function profile()
    {
        $data = array(
            'pageTitle' => 'Profile',
        );
        return view('backend/pages/profile');
    }

    public function categories()
    {
        $data = [
            'pageTitle' => 'categories'
        ];
        return view('backend/pages/categories', $data);
    }
}
