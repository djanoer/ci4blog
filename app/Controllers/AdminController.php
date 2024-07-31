<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CIAuth;
use App\Models\Category;
use SSP;
use Mberecall\CodeIgniter\Library\Slugify;
use App\Models\SubCategory;
use PhpParser\Node\Stmt\Catch_;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail', 'CIFunctions'];
    protected $db;

    public function __construct()
    {
        require_once APPPATH . 'ThirdParty/ssp.php';
        $this->db = db_connect();
    }

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
        return view('backend/pages/profile', $data);
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
                $save = $category->save(['name' => $request->getVar('category_name')]);

                if ($save) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Data saved successfully']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong.']);
                }
            }
        }
    }

    public function getCategories()
    {
        //DB Details
        $dbDetails = array(
            'host' => env('database.default.hostname'),  // Menggunakan env() untuk mengambil nilai dari .env
            'user' => env('database.default.username'),
            'pass' => env('database.default.password'),
            'db'   => env('database.default.database'),
        );

        $table = "categories";
        $primaryKey = "id";
        $columns = array(
            array(
                "db" => "id",
                "dt" => 0,
            ),
            array(
                "db" => "name",
                "dt" => 1,
            ),
            array(
                "db" => "id",
                "dt" => 2,
                "formatter" => function ($d, $row) {
                    return "(x) will be added later";
                }
            ),
            array(
                "db" => "id",
                "dt" => 3,
                "formatter" => function ($d, $row) {
                    return "<div class='btn-group'>
                            <button class='btn btn-sm btn-link p-0 mx-1 editCategoryBtn' data-id='" . $row['id'] . "'>Edit</button>
                            <button class='btn btn-sm btn-link p-0 mx-1 deleteCategoryBtn' data-id='" . $row['id'] . "'>Delete</button>        
                    </div>";
                }
            ),
            array(
                "db" => "ordering",
                "dt" => 4,
            ),
        );

        return json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
        );
    }

    public function getCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('category_id');
            $category = new Category();
            $category_data = $category->find($id);
            return $this->response->setJSON(['data' => $category_data]);
        }
    }

    public function updateCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('category_id');
            $validation = \Config\Services::validation();

            $this->validate([
                'category_name' => [
                    'rules' => 'required|is_unique[categories.name.id,' . $id . ']',
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
                // return $this->response->setJSON(['status'=>1,'token'=>csrf_hash(),'msg'=>'Validated...']);
                $category = new Category();
                $update = $category->where('id', $id)->set(['name' => $request->getVar('category_name')])->update();

                if ($update) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Category has been successfully update.']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong.']);
                }
            }
        }
    }

    public function deleteCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('category_id');
            $category = new Category();

            $delete = $category->delete($id);

            if ($delete) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Category has been successfully deleted.']);
            } else {
                return $this->response->setJSON(['status' => 0, 'msg' => 'Something went wrong.']);
            }
        }
    }

    public function reorderCategories()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $positions = $request->getVar('positions');
            $category = new Category();

            foreach ($positions as $position) {
                $index = $position[0];
                $newPosition = $position[1];
                $category->where('id', $index)->set(['ordering' => $newPosition])->update();
            }
            return $this->response->setJSON(['status' => 1, 'msg' => 'Category ordering has been successfully updated.']);
        }
    }

    public function getParentCategories()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('parent_category_id');
            $options = '<option value="0">Uncategorized</option>';
            $category = new Category();
            $parent_categories = $category->findAll();

            if (count($parent_categories)) {
                $added_options = '';
                foreach ($parent_categories as $parent_category) {
                    $isSelected = $parent_category['id'] == $id ? 'selected' : '';
                    $added_options .= '<option value="' . $parent_category['id'] . '" ' . $isSelected . '>' . $parent_category['name'] . '</option>';
                }
                $options = $options . $added_options;
                return $this->response->setJSON(['status' => 1, 'data' => $options]);
            } else {
                return $this->response->setJSON(['status' => 1, 'data' => $options]);
            }
        }
    }

    public function addSubCategory()
    {

        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'subcategory_name' => [
                    'rules' => 'required|is_unique[sub_categories.name]',
                    'errors' => [
                        'required' => 'Sub category name is required',
                        'is_unique' => 'Sub category name is already exists'
                    ]
                ]
            ]);

            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON([
                    'status' => 0, 'token' => csrf_hash(),
                    'error' => $errors
                ]);
            } else {
                // return $this->response->setJSON(['status'=>1,'token'=>csrf_hash(),
                // 'msg'=>'Validated...']);
                $subcategory = new SubCategory();
                $subcategory_name = $request->getVar('subcategory_name');
                $subcategory_description = $request->getVar('description');
                $subcategory_parent_category = $request->getVar('parent_cat');
                $subcategory_slug = Slugify::model(SubCategory::class)->make($subcategory_name);

                $save = $subcategory->save([
                    'name' => $subcategory_name,
                    'parent_cat' => $subcategory_parent_category,
                    'slug' => $subcategory_slug,
                    'description' => $subcategory_description,
                ]);

                if ($save) {
                    return $this->response->setJSON([
                        'status' => 1, 'token' => csrf_hash(),
                        'msg' => 'New Sub category has been added.'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0, 'token' => csrf_hash(),
                        'msg' => 'Something went wrong.'
                    ]);
                }
            }
        }
    }

    public function getSubCategories(){
        $category = new Category();
        $subcategory = new SubCategory();

        //DB Details
        $dbDetails = array(
            'host' => env('database.default.hostname'),  // Menggunakan env() untuk mengambil nilai dari .env
            'user' => env('database.default.username'),
            'pass' => env('database.default.password'),
            'db'   => env('database.default.database'),
        );
        $table = "sub_categories";
        $primaryKey = "id";
        $columns = array(
            array(
                "db" => "id",
                "dt" => 0,
            ),
            array(
                "db" => "name",
                "dt" => 1,
            ),
            array(
                "db" => "id",
                "dt" => 2,
                "formatter" => function ($d, $row) use ($category, $subcategory) {
                    $parent_cat_id = $subcategory->asObject()->where("id",$row['id'])->first()->parent_cat;
                    $parent_cat_name = ' - ';
                    if( $parent_cat_id != 0 ){
                        $parent_cat_name = $category->asObject()->where('id',$parent_cat_id)->first()->name;
                    }
                    return $parent_cat_name;
                }
            ),
            array(
                "db" => "id",
                "dt" => 3,
                "formatter"=>function($d, $row){
                    return "(x) will be added later";
                }
            ),
            array(
                "db" => "id",
                "dt" => 4,
                "formatter" => function ($d, $row) {
                    return "<div class='btn-group'>
                            <button class='btn btn-sm btn-link p-0 mx-1 editSubCategoryBtn' data-id='" . $row['id'] . "'>Edit</button>
                            <button class='btn btn-sm btn-link p-0 mx-1 deleteSubCategoryBtn' data-id='" . $row['id'] . "'>Delete</button>        
                    </div>";
                }
            ),
            array(
                "db" => "ordering",
                "dt" => 5,
            ),
        );

        return json_encode(
            SSP::simple($_GET,$dbDetails, $table, $primaryKey, $columns)
        );
    }

    //fungsi new post
    public function addPost() {
        $subcategory = new SubCategory();
        $data = [
            'pageTitle'=>'Add new post',
            'categories'=>$subcategory->asObject()->findAll()
        ];
        return view('backend/pages/new-post', $data);
    }
}
