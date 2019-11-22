<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Validator;

use App\Http\Resources\UserResource;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        /*this will be always on the latest*/
        $result = User::orderBy('created_at','desc')->get();

        if(isset($request->search) ) {
            $result = full_text_search($result, $request->search, ["id", "created_at", "updated_at", "email_verified_at"]);
        }

        return UserResource::collection(paginate($result , $request->itemsPerPage , $request->page));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return UserResource
     */
    public function update(Request $request,  $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required'
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(),422);

        }


        $user = User::findOrFail($id);
        $user->fill($request->all())->save();

        $return = ["status" => "Success",
            "error" => [
                "code" => 201,
                "errors" => 'Deleted'
            ]];
        return response()->json($return, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        User::whereId($id)->delete();
        $return = ["status" => "Success",
            "error" => [
                "code" => 200,
                "errors" => 'Deleted'
            ]];
        return response()->json($return, 200);
    }
}

