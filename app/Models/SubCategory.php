<?php

namespace App\Models;

use CodeIgniter\Model;
use Mberecall\CodeIgniter\Library\Slugify;

class SubCategory extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sub_categories';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'name', 'slug', 'parent_cat', 'description', 'ordering'
    ];

    public function updateSlug($id, $name){

        //Generate a new slug based on the provided name
        $slug = Slugify::model(SubCategory::class)->sid($id)->make($name);

        //update the subcategory with the new slug
        $this->update($id, ['slug'=>$slug]);
  
    }
}
