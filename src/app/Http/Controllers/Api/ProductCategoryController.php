<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $keyword = $request->input('keyword');
       $limit = $request->input('limit', 10);
       $product_categories = ($keyword) ? 
                            ProductCategory::where('categories_name', 'like', '%' . $keyword . '%')->OrderBy('id', 'desc'): 
                            ProductCategory::OrderBy('id', 'desc');
       return Helper::success('Product Category retrieve Successfully',$product_categories->paginate($limit));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = $this->rules_validation();
            $requestForm = $this->collect_request();
            if($rules->fails()){
                return Helper::error(null, $rules->errors());
            }
            $productCategories = ProductCategory::create($requestForm);
            return Helper::success($productCategories, 'Product Categories Created Successfully',201);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        try {
            $productCategories = $productCategory->first();
            return Helper::success($productCategories,'Detail Product Categories Retrieve Successfully', 201);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            $rules = $this->rules_validation();
            $requestForm = $this->collect_request();
            if($rules->fails()){
                return Helper::error($rules->errors(), null);
            }
            $productCategory = ProductCategory::find($id);
            if($productCategory){
                $productCategories = $productCategory->update($requestForm);
                return Helper::success($productCategories, 'Product Categories Updated Successfully');
            }
            return Helper::error(null, "Product Categories doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $productCategory = ProductCategory::find($id);
            if($productCategory){
                $actionQuery = $productCategory->delete();
                return Helper::success($actionQuery, 'Product Categories deleted Successfully');
            }
            return Helper::error(null, "Product Categories doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }

    protected function rules_validation()
    {
        $schema = Validator::make(request()->all(),[
            'categories_name' => 'required',
            'categories_description' => 'required',
            'notes' => 'string'
        ]);

        return $schema;
    }

    protected function collect_request()
    {
        $request = [
            'categories_name' => request()->input('categoryName'),
            'categories_description' => request()->input('categoryDescription'),
            'notes' => request()->input('notes')
        ];
        return $request;
    }
}
