<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendRequestController extends Controller
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
        $requests = FriendRequest::where('friend_id', $user_id)
            ->join('users', 'friend_requests.friend_id', '=', 'users.id')
            ->where('friend_requests.status', config('global.pending'))
            ->select('friend_requests.id', 'users.id as user_id', 'users.name', 'users.gender', 'users.profile_pic')
            ->orderBy('friend_requests.created_at', 'ASC')
            ->get();

        $data = [
            'message' => 'user request list',
            'status' => true,
            'data' => $requests
        ];

        return response()->json($data, 200);
    }

    public function friendship()
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

        // check either this user exist in the databse
        if ($this->check_user($user_id) == false) {
            return $this->general_error_with('Alert: No User found against this ID: ' . $user_id);
        }
        if ($this->check_user($friend_id) == false) {
            return $this->general_error_with('Alert: No Friend found against this ID: ' . $friend_id);
        }
        // check either they are alreay connected or not.
        if (($this->is_already_friend($user_id, $friend_id) == true) || $this->is_already_friend($friend_id, $user_id) == true) {
            return $this->general_error_with('Already connected id = ' . $friend_id);
        }

        // update request status to to accept or reject
        
        $this->update_request_status($user_id, $friend_id, $status);

        // if status is one then send a friend request
        if ($status == 1) {
            return $this->create_friend($user_id, $friend_id);
        } else {
            return $this->general_success_with('ignore successfull');
        }
        
    }

    // Custom Funcstion extension.

    public function is_already_friend($user_id, $friend_id)
    {
        # code...
        $request = Friend::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->first();

        if ($request != null) {
            return true;
        } else {
            return false;
        }
    }

    public function check_user($user_id)
    {
        # code...
        $user = User::where('id', $user_id)->first();
        if ($user == null) {
            return false;
        }
        return true;
    }

    public function create_friend($user_id, $friend_id)
    {
        # code...
        $data = [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'block' => 0
        ];

        // fire a notifcation at firiend Id.
        $request = Friend::create($data);

        return response()->json([
            'message' => 'Connection created with id =  ' . $friend_id,
            'status' => true,
            'data' => (object)[]
        ], 200,);;
    }

    public function update_request_status($user_id, $friend_id, $status)
    {
        // to keep the track of each request.
        $friend_request = FriendRequest::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->first();

        if ($friend_request == null) {
            return;
        }

        if ($status == 1) {
            $request_status = config('global.accepted');
        } else {
            $request_status = config('global.rejected');
        }

        $friend_request->status = $request_status;
        $friend_request->save();
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
