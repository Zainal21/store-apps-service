<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use Midtrans\Snap;
use App\Models\{
    Cart,
    Order,
    ApiTokenLog,
    OrderDetail,
    Province,
    District
};
use Midtrans\Config;
use App\Helpers\Helper;
use Midtrans\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $rules = $this->rules_validation($request->all());
            if($rules->fails()){
                return Helper::error(null, $rules->errors(), 400);
            }
            $user = JWTAuth::user();
            $cart = Cart::with(['product', 'user'])->where(['user_id' => $user->id])->get();
            // store to order table
            if(count($cart) > 0){
                DB::beginTransaction();
                try {
                    $applicationSetting                    = Helper::SettingApplication();
                    $requestOrderForm['user_id']           = $user->id;
                    $requestOrderForm['order_unique_code'] = $this->create_order_inv();
                    $requestOrderForm['province_id']      = $request->input('provinceId');
                    $requestOrderForm['district_id']      = $request->input('districtId');
                    $requestOrderForm['address']          = $request->input('address');
                    $requestOrderForm['amount']           = $request->input('amount') ?? 0;
                    $requestOrderForm['payment_channel_id'] = $request->input('paymentChannelId') ?? 0;
                    $requestOrderForm['is_insurance_fee'] = $request->input('isInsuranceFee') ?? 0;
                    $requestOrderForm['packing_fee']      = $request->input('packingFee') ?? 0;
                    $requestOrderForm['ppn']              = $applicationSetting->ppn_persentage;
                    $requestOrderForm['ppn_fee']          = ($applicationSetting->ppn_persentage / 100) * $requestOrderForm['amount'];
                    $requestOrderForm['insurance_fee']    = ($requestOrderForm['is_insurance_fee'] == 1) ? $applicationSetting->insurace_price : 0;
                    $requestOrderForm['total_amount']     = ($requestOrderForm['amount'] + $requestOrderForm['insurance_fee'] + $requestOrderForm['ppn_fee'] + $requestOrderForm['packing_fee']);
                    $requestOrderForm['status']           = 'PENDING';
                    $requestOrderForm['expired_at']       = date('Y-m-d H:i:s', strtotime('1 days'));
                    $order = Order::create($requestOrderForm);
                    // store order detail
                    foreach($cart as $itemCart){
                        $collectCart = [
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'product_id' =>  $itemCart->product_id,
                            'total_price' => $itemCart->total_price,
                            'notes' =>  $itemCart->notes,
                            'shipping_status' => 'PENDING',
                        ];
                        OrderDetail::create($collectCart);
                    }
                    // delete cart
                    Cart::with(['product', 'user'])->where(['user_id' => $user->id])->delete();
                    // set configuration midtrans
                    $this->MidtransConfiguration();
                    $midtransPayload = [
                        'transaction_details' => [
                            'order_id' =>  $order->order_unique_code,
                            'gross_amount' => (int) $order->total_amount,
                        ],
                        'customer_details' => [
                            'first_name'    => $user->name,
                            'email'         => $user->email
                        ],
                        'enabled_payments' => ['gopay','bank_transfer'],
                        'vtweb' => []
                    ];
                    try {
                        $paymentUrl = Snap::createTransaction($midtransPayload)->redirect_url;
                        DB::commit();
                        $finalResponse = [
                            'success' => true,
                            'paymentUrl' => $paymentUrl,
                            'transaction_detail' => $midtransPayload['transaction_details'],
                            'customer_details' => $midtransPayload['customer_details'],
                        ];
                        return Helper::success($finalResponse, 'Order created Successfully');
                    } catch (\Exception $th) {
                        DB::rollback();
                        return Helper::error(null, $th->getMessage() ?? 'Failed when Connected to Midrans Payment Gateway', 500);
                    }
                } catch (\Exception $th) {
                    DB::rollback();
                    return Helper::error(null, $th->getMessage() ?? 'Failed when created Order', 500);
                }
            }else{
                return Helper::error(null, 'Cart Item not found', 404);
            }
    }


    public function midrans_callback()
    {
        // Set configuration in  midtrans
        $this->MidtransConfiguration();;

        $notification = new Notification();

        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;
        
        $order = Order::where('order_unique_code', $order_id);
        if ($status == 'capture') {
            if ($type == 'credit_card'){
                if($fraud == 'challenge'){
                    $order->status = 'PENDING';
                }
                else {
                    $order->status = 'SUCCESS';
                }
            }
        }
        else if ($status == 'settlement'){
            $order->status = 'SUCCESS';
        }
        else if($status == 'pending'){
            $order->status = 'PENDING';
        }
        else if ($status == 'deny') {
            $order->status = 'CANCELLED';
        }
        else if ($status == 'expire') {
            $order->status = 'CANCELLED';
        }
        else if ($status == 'cancel') {
            $order->status = 'CANCELLED';
        }
        $order->save();
        if($order){
            return Helper::success($order, ' Midtrans Notification Success');
        }else{
            return Helper::error(null, 'Failed when Send Callback Notification', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success(Request $request)
    {
        return view('midtrans.success');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unfinish(Request $request)
    {
        return view('midtrans.unfinish');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function error(Request $request)
    {
        return view('midtrans.error');
    }

    protected function rules_validation($data)
    {
        $rules = [
            'provinceId' => ['required', function ($attribute, $value, $fail) use ($data) {
                $provinceId = $data['provinceId'];
                $isExists = Province::find($provinceId);
                if (!$isExists > 0) {
                    $fail('province Doesnt Exist');
                }
            },],
            'districtId' => ['required', function ($attribute, $value, $fail) use ($data) {
                $districtId = $data['districtId'];
                $isExists = District::find($districtId);
                if (!$isExists > 0) {
                    $fail('District Doesnt Exist');
                }
            },],
            'address' => ['required']
        ];
        $schema = Validator::make(request()->all(),$rules);
        return $schema;
    }

    protected function MidtransConfiguration()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', '#');
        Config::$isProduction = env('MIDTRANS_ISPRODUCTION', '#');
        Config::$isSanitized = env('MIDTRANS_CLIENT_KEY', '#');
        Config::$is3ds = env('MIDTRANS_IS3DS');
    }

    protected function create_order_inv()
    {
        $day = date('d');
        $month = date('m');
        $years = date('Y');
        $yearFormat = date('y');
        $baseOrdered = "00000";
        $data = Order::selectRaw('max(RIGHT(order_unique_code, 4)) as last_order')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $years)
                ->whereDay('created_at', $day)
                ->orderBy(DB::raw('max(RIGHT(order_unique_code, 4))', 'DESC'))->take(1)->first();
        if ($data) $baseOrdered = $data->last_order;
        $nextOrdered = abs($baseOrdered) + 1;
        $uniqueCode = 'ORDER' . $day . $month . $yearFormat . sprintf('%05d', $nextOrdered);
        return $uniqueCode;
    }
}
