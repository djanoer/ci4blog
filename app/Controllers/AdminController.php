<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CIAuth;
use App\Models\Category;
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

    public function addCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'category_name' => [
                    'rules' => 'required|is_unique[categories.name]',
                    'errors' => [
                        'required' => 'Category name is required',
                        'is_unique' => 'Category name already exists'
                    ]
                ]
            ]);

            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'error' => $errors]);
            } else {
                // return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg'=>'Data saved successfully']);
                $category = new Category();
                $save = $category->save(['name'=>$request->getVar('category_name')]);

                if($save){
                    return $this->response->setJSON(['status'=>1, 'token'=>csrf_hash(),'msg'=>'Data saved successfully']);
                }else{
                    return $this->response->setJSON(['status'=>0, 'token'=>csrf_hash(),'msg'=>'Something went wrong.']);
                }
            }
        }
    }
}
