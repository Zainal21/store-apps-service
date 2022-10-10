<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
    public function index()
    {
        $keyword = request()->input('keyword');
        $role = request()->input('role');
        $limit = request()->input('limit', 5);
        $users = User::orderBy('id', 'desc');
        if($keyword){
            $users->where('name','like', '%' . $keyword . '%');
        }
        if($role){
            $users->where('role', $role);
        }
        return Helper::success('Users retrieve Successfully',$users->paginate($limit));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->rules_validation();
        if($rules->fails()){
            return Helper::error(null, $rules->errors(), 400);
        }
        $requestForm = $this->collect_request();
        User::create($requestForm);
        return Helper::success($actionQuery, 'User Updated Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return Helper::success($user,'Detail user Retrieve Successfully');
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $rules = $this->rules_validation($id);
        if($rules->fails()){
            return Helper::error(null, $rules->errors(), 500);
        }
        $requestForm = $this->collect_request();
        $user = User::find($id);
        if($user){
            $user->update($requestForm);
            return Helper::success($actionQuery, 'User Updated Successfully');
        }
        return Helper::error(null, "User doesn't Exist", 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if($user){
                $actionQuery = $user->delete();
                return Helper::success($actionQuery, 'User deleted Successfully');
            }
            return Helper::error(null, "User doesn't Exist", 404);
        } catch (\Throwable $th) {
            return Helper::error(null, $th->getMessage() ?? 'Internal server error', 500);
        }
    }

    protected function rules_validation($id = false)
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ];
        if($id) unset($rules['password']);
        $schema = Validator::make(request()->all(),$rules);
        return $schema;
    }

    protected function collect_request()
    {
        $request = [
            'name' => request()->input('name'),
            'email' =>  request()->input('email'),
            'password' => bcrypt(request()->input('password')),
            'role' => request()->input('role') ?? 2
        ];
        return $request;
    }
}
