<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    //


    /**
     * This function will gather the data from different tables
     * and show them in user's shop tab.
     * Need to @return 3 thing.
      1. Banner Image
      2. Feature Products 
      3. Recent products
     */

    public function friends()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user_id = request('user_id');
        $user = User::find($user_id);
        if ($user == null) {
            return $this->general_error_with('No user found againes this ID');
        }
        $user = $user->join('education', 'education.user_id', '=', 'users.id')->first();

        // get those user that are not in the suggestion.
        // get top 20 user and append it into the suggestion table.
        $friends =
            User::whereDoesntHave('suggestions', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with("post_psbs")
            // ->join('education', 'education.user_id', '=', 'users.id')
            // ->where('city', 'like', '%' . $user->city . '%')
            // ->where('institute_id', 'like', '%' . $user->institute_id . '%')
            // ->where('course', 'like', '%' . $user->course . '%')
            ->limit(10)
            ->get();

        // check if firieds cout is less then 2 then append some random user

        // add these suggestion into suggestion table.
        $this->create_suggestion($user_id, $friends);

        $data = [
            'message' => 'user friends suggestions',
            'status' => true,
            'data' => $friends
        ];

        return response()->json($data, 200);
    }
    // POST function
    public function friend_request()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
            'friend_id' => 'required|string',
            'status' => 'required|string',
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
        // check either request is already sent
        if ($this->is_request_sent($user_id, $friend_id) == true) {
            return $this->general_error_with('Alert: request already sent');
        }

        // alter suggestion table and change the status to like or dislike.
        $this->update_suggestion_status($user_id, $friend_id, $status);

        // if status is one the send a friend request
        if ($status == 1) {
            return $this->create_friend_request($user_id, $friend_id);
        } else {
            return $this->general_success_with('dislike successfull');
        }
        
    }
    public function is_request_sent($user_id, $friend_id)
    {
        # code...
        $request = FriendRequest::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->first();

        if ($request != null) {
            return true;
        } else {
            return false;
        }
    }
    // send friend request from user_id to request
    public function create_friend_request($user_id, $friend_id)
    {
        # code...
        $data = [
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'status' => config('global.pending')
        ];

        // fire a notifcation at firiend Id.
        $request = FriendRequest::create($data);

        return response()->json([
            'message' => 'request send successfully',
            'status' => true,
            'data' => (object)[]
        ], 200,);;
    }

    public function update_suggestion_status($user_id, $friend_id, $status)
    {
        # you need to check the two way connection. Change the status 
        // to suggested for both users.
        $suggestion = Suggestion::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->first();

        if ($suggestion == null) {
            return;
        }

        if ($status == 1) {
            $updated_suggestion = config('global.like');
        } else {
            $updated_suggestion = config('global.dislike');
        }

        $suggestion->status = $updated_suggestion;
        $suggestion->save();
    }

    public function test()
    {
        # code...


        $user_id = '7';

        // get those user that are not in the suggestion.
        $suggestions =
            User::whereDoesntHave('suggestions', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->join('education', 'education.user_id', '=', 'users.id')
            ->where('city', 'like', '%' . "lahore" . '%')
            ->get();
        // get top 20 user and append it into the suggestion table.

        // Suggestion::create(
        //     ['user_id' => $user_id,
        //     'friend_id' => $suggestions->id,
        //     'status' => 'pending'
        //     ]);

        return response()->json($suggestions, 200);
    }

    public function create_suggestion($user_id, $friends)
    {
        # code...
        foreach ($friends as $friend) {
            # code...
            Suggestion::create(
                [
                    'user_id' => $user_id,
                    'friend_id' => $friend->id,
                    'status' => config('global.pending')
                ]
            );
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
