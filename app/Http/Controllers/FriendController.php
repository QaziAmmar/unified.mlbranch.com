<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    //

    public function list()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user_id = request('user_id');
        // get all the firend request by recent created order by
        $requests = Friend::where('user_id', $user_id)
            ->join('users', 'friends.friend_id', '=', 'users.id')
            ->where('friends.block', 0)
            ->select('friends.friend_id as user_id', 'users.name', 'users.gender', 'users.profile_pic', 'users.firebase_id')
            ->orderBy('users.name', 'ASC')
            ->get();

        $data = [
            'message' => 'user request list',
            'status' => true,
            'data' => $requests
        ];

        return response()->json($data, 200);
    }

    public function block()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required',
            'friend_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user_id = request('user_id');
        $friend_id = request('friend_id');
        $status = request('status');

        return $this->update_user_relation($user_id, $friend_id, $status);
    }


    public function update_user_relation($user_id, $friend_id, $status)
    {
        // get user realtion with two wasy relation
        $relation = $this->get_friend_relation($user_id, $friend_id);
        if ($relation == null) {
            return $this->general_error_with('No friend relation founded');
        }
        
        // check if user is already block of unblocked
        // dd($relation, $relation->block);
        
        if ($status == $relation->block) {
            // block user
            if ($status == "1") {
                return $this->general_error_with('alreay blocked');
            } else {
                return $this->general_error_with('already unblocked');
            }
            
        }

        $relation->block = $status;
        $relation->save();

        if ($status == 1) {
            // block user
           return $this->general_success_with('user blocked');
        } else {
            // unblock user
            return $this->general_success_with('user unblocked');
        }
    }

    public function get_friend_relation($user_id, $friend_id)
    {
        # code...
        return Friend::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->first();
    }

    public function validationError($validator)
    {
        # code...
        return response()->json([
            'message' => 'Error: Incorrect or missing parameters',
            'status' => false,
            'data' => $validator->errors()
        ], 401);
    }

    public function general_error_with($message)
    {
        # code...
        return response()->json([
            'message' => $message,
            'status' => false,
            'data' => (object)[]
        ], 401);
    }

    public function general_success_with($message)
    {
        # code...
        return response()->json([
            'message' => $message,
            'status' => true,
            'data' => (object)[]
        ], 200);
    }
}
