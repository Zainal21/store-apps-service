<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use App\Models\Cart;
use App\Helpers\Helper;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $cartItem = Cart::with(['product', 'user'])->where(['user_id' => JWTAuth::user()->id])->OrderBy('id', 'asc')->get();
       return Helper::success('Cart Items retrieve Successfully',$cartItem);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestForm = $this->collect_request();
        $schema = $this->rules_validation($requestForm);
        try {
            if($schema->fails()){
                return Helper::error(null,$schema->errors());
            }
            $product = Product::findOrfail($requestForm['product_id']);
            $requestForm['normal_price'] = $product->price_sale;
            $requestForm['total_price'] = $product->price_sale * $requestForm['qty'];
            $product = Cart::create($requestForm);
            return Helper::success($product, 'Product Added to Cart Successfully',201);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requestForm = $this->collect_request();
        $schema = $this->rules_validation($requestForm);
        try {
            if($schema->fails()){
                return Helper::error(null,$schema->errors());
            }
            $CartItem = Cart::find($id);
            if($CartItem){
                $product = Product::findOrfail($requestForm['product_id']);
                $requestForm['normal_price'] = $product->price_sale;
                $requestForm['total_price'] = $product->price_sale * $requestForm['qty'];
                $cart = $CartItem->update($requestForm);
                return Helper::success($product, 'Cart Item Updated Successfully',200);
            }
            return Helper::error(null, 'Cart Item Doesnt Exist',404);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $CartItem = Cart::find($id);
            if($CartItem){
                $cart = $CartItem->delete();
                return Helper::success($cart, 'Cart Item Deleted Successfully',201);
            }
            return Helper::error(null, 'Cart Item Doesnt Exist',404);
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    protected function rules_validation($data)
    {
        $rules = [
            'productId' => ['required', function ($attribute, $value, $fail) use ($data) {
                $productID = $data['product_id'];
                $isExists = Product::find($productID);
                if (!$isExists > 0 || $isExists->is_available == 0) {
                    $fail('Products Doesnt Exist');
                }
            },],
            'qty' => ['required']
        ];
        $schema = Validator::make(request()->all(),$rules);
        return $schema;
    }

    protected function collect_request()
    {
        $request = [
            'user_id' => JWTAuth::user()->id,
            'product_id' => request()->input('productId'),
            'qty' => request()->input('qty'),
        ];
        return $request;
    }
}
