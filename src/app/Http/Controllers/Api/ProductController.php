<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Helpers\Helper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductController extends Controller
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
        $products = Product::with(['product_gallery','product_category']);
        if($keyword){
            $products
                ->where('product_name','like', '%' . $keyword . '%')
                ->or_where('product_name','like', '%' . $keyword . '%');
        }
        // if($category){
        //     $products
        //     ->wherehas('product_category',function($query){
        //        $query->where('categories_name', 'id', '=', $category);
        //     });
        // }
        return Helper::success($products->paginate($limit), 'Data Products Retrieve Successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $schema = $this->rules_validation();
        $requestForm = $this->collect_product();
        try {
            if($schema->fails()){
                return Helper::error(null,$schema->errors());
            }
            $product = Product::create($requestForm);
            return Helper::success($product, 'Product Created Successfully',201);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product_detail = Product::findOrfail($id);
            return Helper::success($product_detail,'Detail Product Detail Retrieve Successfully');
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schema = $this->rules_validation();
        $requestForm = $this->collect_product($id);
        try {
            if($schema->fails()){
                return Helper::error($schema->errors(), null);
            }
            $product = Product::find($id);
            if($product){
                $product = $product->update($requestForm);
                return Helper::success($product, 'Product Updated Successfully');
            }
            return Helper::error(null, "Product doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if($product){
                $actionQuery = $product->delete();
                return Helper::success($actionQuery, 'Product deleted Successfully');
            }
            return Helper::error(null, "Product doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }


    protected function rules_validation()
    {
        $schema = Validator::make(request()->all(), [
            'productName' => 'required',
            'productDescription' => 'required',
            'productCategoryId' => 'required',
            'priceSale' => 'required',
            'discount' => 'required',
            'discountPersentage' => 'required',
            'is_available' => 'required',
        ]);

        return $schema;
    }

    protected function collect_product($id = false)
    {
       $requestForm = [
            'product_code' => $this->create_product_code(),
            'product_name' => request()->input('productName'),
            'product_description' => request()->input('productDescription'),
            'product_category_id' => request()->input('productCategoryId'),
            'price_sale' => request()->input('priceSale'),
            'discount' => request()->input('discount'),
            'discount_persentage' => request()->input('discountPersentage'),
            'is_available' => request()->input('is_available'),
       ];
       if($id) unset($requestForm['product_code']);
       return $requestForm;
    }

    protected function create_product_code()
    {
        $day = date('d');
        $month = date('m');
        $years = date('Y');
        $yearFormat = date('y');
        $baseOrdered = "00000";
        $data = Product::selectRaw('max(RIGHT(product_code, 4)) as last_order')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $years)
                ->whereDay('created_at', $day)
                ->orderBy(DB::raw('max(RIGHT(product_code, 4))', 'DESC'))->take(1)->first();
        if ($data) $baseOrdered = $data->last_order;
        $nextOrdered = abs($baseOrdered) + 1;
        $uniqueCode = 'PRD' . $day . $month . $yearFormat . sprintf('%05d', $nextOrdered);
        return $uniqueCode;
    }
}
