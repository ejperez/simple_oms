<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Product_Category;

class AJAXController extends Controller {

    public function searchProductByCategory(Product_Category $category)
    {
        $category->products;
        return json_encode($category);
	}

    public function searchAddress()
    {
        $term = Input::get('term');
        if(!empty($term)){
            $term = '%'.$term.'%'; // Enclose in wildcards
            $zipcodes = \SimpleOMS\Zipcode::where('major_area', 'like', $term)
                ->where('city', 'like', $term, 'OR')
                ->where('zip_code', 'like', $term, 'OR')
                ->get();

            return json_encode($zipcodes);
        } else {
            abort(404);
        }
    }
}
