<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\ProductGallery;
use App\Http\Controllers\Controller;

class ProductGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keyword = request()->input('keyword');
        $category = request()->input('category');
        $limit = request()->input('limit', 20);
        $product_galleries = ProductGallery::with(['product' => function($query){
            $query->selectRaw('product_code, product_name, id as product_id');
        }]);
        return Helper::success($product_galleries->paginate($limit), 'Data Product Gallery Retrieve Successfully');
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
            if($rules->fails()){
                return Helper::error(null,$rules->errors());
            }
            if($request->file('image')){
                $file = request()->file('image');
                $imageUpload = $file->move('uploads/products-galleries/', Helper::generate_filename($file, 'product-galleries', 'galleries-product-'));
            }
            $product_galleries = ProductGallery::create([
                'products_id' => $request->input('productId'),
                'image' => !empty($imageUpload) ? $imageUpload : ''
            ]);
            return Helper::success($product_galleries,'Product Galleries Created Successfully', 201);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductGallery  $productGallery
     * @return \Illuminate\Http\Response
     */
    public function show(ProductGallery $productGallery)
    {
        try {
            $product_galleries = $productGallery->with(['product' => function($query){
                $query->selectRaw('product_code, product_name, id as product_id');
            }])->first();
            return Helper::success($product_galleries,'Detail Product Gallery Retrieve Successfully');
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductGallery  $productGallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = $this->rules_validation($id);
        try {
            if($rules->fails()){
                return Helper::error($rules->errors(), null);
            }
            $productGallery = ProductGallery::find($id);
            if($productGallery){
                if($request->file('image')){
                    $file = request()->file('image');
                    $imageUpload = $file->move('uploads/products-galleries/', Helper::generate_filename($file, 'product-galleries', 'galleries-product-'));
                    Helper::remove_file($productGallery->image);
                }
                $actionQuery = $productGallery->update([
                    'image' => !empty($imageUpload) ? $imageUpload : $productGallery->image
                ]);
                return Helper::success($actionQuery, 'Product Gallery Updated Successfully');
            }
            return Helper::error(null, "Product doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductGallery  $productGallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $productGallery = ProductGallery::find($id);
            if($productGallery){
                Helper::remove_file($productGallery->image);
                $actionQuery = $productGallery->delete();
                return Helper::success($actionQuery, 'Product Gallery deleted Successfully');
            }
            return Helper::error(null, "Product Gallery doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }


    protected function rules_validation($id = false)
    {
        $rules = [
            'productId' => 'required',
            'notes' => 'string'
        ];
        $typeRules = 'required|image|max:2048';
        if($id){
            $typeRules = 'image|max:2048';
            unset($rules['productId']);
        }
        $rule_image = ['image' => $typeRules];
        $schema = Validator::make(request()->all(),array_merge($rules, $rule_image));
        return $schema;
    }
}
