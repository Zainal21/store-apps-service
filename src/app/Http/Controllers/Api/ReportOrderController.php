<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.checkRole');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_order_report(Request $request)
    {
       $keyword = $request->input('keyword');
       $limit = $request->input('limit', 10);
       $product_categories = ($keyword) ? 
                            ProductCategory::where('categories_name', $keyword)->paginate($limit) : 
                            ProductCategory::paginate($limit);
       return Helper::success('Product Category retrieve Successfully',$product_categories);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function order_export_excel(Request $request)
    {
       $keyword = $request->input('keyword');
       $limit = $request->input('limit', 10);
       $product_categories = ($keyword) ? 
                            ProductCategory::where('categories_name', $keyword)->paginate($limit) : 
                            ProductCategory::paginate($limit);
       return Helper::success('Product Category retrieve Successfully',$product_categories);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function order_preview_pdf(Request $request)
    {
       $keyword = $request->input('keyword');
       $limit = $request->input('limit', 10);
       $product_categories = ($keyword) ? 
                            ProductCategory::where('categories_name', $keyword)->paginate($limit) : 
                            ProductCategory::paginate($limit);
       return Helper::success('Product Category retrieve Successfully',$product_categories);
    }

}
