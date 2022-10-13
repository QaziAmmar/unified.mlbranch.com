<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
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

    protected $subscription_array = [
        'change_gender_filter', 'remove_ads', 'create_business', 'unlimited_matches',
        'unlimited_swipes', 'spotlight', 'get_featured', 'message'
    ];

    public function create()
    {
        # code...
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
            'change_gender_filter' => 'required|string',
            'remove_ads' => 'required|string',
            'create_business' => 'required|string',
            'unlimited_matches' => 'required|string',
            'unlimited_swipes' => 'required|string',
            'spotlight' => 'required|string',
            'get_featured' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::where('id', request('user_id'))->first();
        if ($user == null) {
          return $this->general_error_with("No user Found");
        }

        // here we need to check 2 conditions.
        // 1. Is user is subscribe 
        // 2. If days limit exceed then unsubscribe

        $subscription_status = $this->isSubscribed(request('user_id'));

        // check is subscribe already or not.
        if ($subscription_status['is_subscribed'] == true) {
           return $this->general_error_with("Already subscribed and expire at after " . $subscription_status['days']);
        }

        $subscription = request([
            'user_id', 'change_gender_filter', 'remove_ads', 'create_business', 'unlimited_matches',
            'unlimited_swipes', 'spotlight', 'get_featured', 'message'
        ]);

        $subscription = $this->replace_zero_with_empty_string($subscription);
        $subscription['status'] = true;
        // update the existing value.
        $subscription_ = tap(Subscription::where('user_id', request('user_id')))
            ->update($subscription)
            ->first();
            // make a history
        $subscription['user_id'] = request('user_id');
        $this->createHistory($subscription);

        // After creating the History make changes the database on the base of purchased subscription
        $this->update_business_and_products_by($subscription_);

        return response()->json([
            'message' => 'Subscription added successfully',
            'status' => true,
            'data' => $subscription_
        ], 200);
    }

    /**
     * Api function which take the user_id as an request
     */
    public function unsubscribed()
    {
        # code...
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $this->unsubscribe_by(request('user_id'));

        return response()->json([
            'message' => 'Unsubscribe successfully',
            'status' => true,
            'data' => (object)[]
        ], 200);
    }
    /**
     * Unit function which will get the user_id and make the user status as un subscribe.
     */
    public function unsubscribe_by($user_id)
    {
        # code...
        $subscription = $this->get_subscription_default_array();
        Subscription::where('user_id', $user_id)->update($subscription);
        $subscription['user_id'] = $user_id;
        $this->createHistory($subscription);
    }
    /**
     * Unit function return the current status of subscription.
     */
    public function subscription_status()
    {
        # code...
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $subscription_status = $this->isSubscribed(request('user_id'));

        return response()->json([
            'message' => $subscription_status['message'],
            'status' => $subscription_status['is_subscribed'],
            'data' => $subscription_status['subscription']
        ], 200);
    }
    /**
     * Unit function that checks either user is subscribe or not with some other information
     */
    public function isSubscribed($user_id)
    {
        # code...

        $subscription = Subscription::where('user_id', $user_id)->first();

        if ($subscription == null) {
            $status['days'] = 0;
            $status['subscription'] = (object)[];
            $status['is_subscribed'] = false;
            $status['message'] = "No user Found";
            return $status;
        }


        $days = (int) $this->days_count_from($subscription->updated_at);
        $is_subscribed = $subscription->status;

        // check if days are in negative and you have some value in subscription table column it means that you already subscribed.

        $status['days'] = $days;
        if ($is_subscribed == false) {
            $status['is_subscribed'] = false;
            $status['message'] = "No current active subscription first";
            $status['subscription'] = (object)[];
        }
        if (($days >= 0) &&  ($is_subscribed)) {
            // agher din zyada ho gy hn or abi tk ap ka status active hai to us ko false kna ho ga.
            $status['is_subscribed'] = false;
            $status['message'] = "No current active subscription";
            $status['subscription'] = (object)[];
            // call the function which will change your subscription status to false
            $this->unsubscribe_by($user_id);
        }
        if (($days <= 0) &&  ($is_subscribed)) {
            $status['is_subscribed'] = true;
            $status['message'] = "You have an active subsciption ends after " . $days;
            $status['subscription'] = $subscription;
        }

        return $status;
    }

    /**
     * This functuion runs after every one hour and check if you are featured then updated time
     * is greated then 24 hourn then it will unsubscired you to featured.
     */
    public function subscription_service_routine($user_id)
    {
        # code...

        $subscription = Subscription::where('user_id', $user_id)->first();

        if ($subscription == null) {
            $status['days'] = 0;
            $status['subscription'] = (object)[];
            $status['is_subscribed'] = false;
            $status['message'] = "No user Found";
            return $status;
        }


        $days = (int) $this->hours_count_from($subscription->updated_at);
        $is_subscribed = $subscription->status;

        // check if days are in negative and you have some value in subscription table column it means that you already subscribed.

        $status['days'] = $days;
        if ($is_subscribed == false) {
            $status['is_subscribed'] = false;
            $status['message'] = "No current active subscription";
            $status['subscription'] = (object)[];
        }
        if (($days >= 0) &&  ($is_subscribed)) {
            // agher din zyada ho gy hn or abi tk ap ka status active hai to us ko false kna ho ga.
            $status['is_subscribed'] = false;
            $status['message'] = "No current active subscription";
            $status['subscription'] = (object)[];
            // call the function which will change your subscription status to false
            $this->unsubscribe_by($user_id);
        }
        if (($days <= 0) &&  ($is_subscribed)) {
            $status['is_subscribed'] = true;
            $status['message'] = "You have an active subsciption ends after " . $days;
            $status['subscription'] = $subscription;
        }

        return $status;
    }


    public function createHistory($subscription)
    {
        # code...
        SubscriptionHistory::create($subscription);
    }

    public function update_business_and_products_by($subscription)
    {
        # code...
        // if get_featured = set any value then make the business feature.
        
        $is_featured = false;
        $featured_at = $subscription->featured_at;
        if  ($subscription['get_featured'] != '') {
            $is_featured = true;
            $featured_at = now();
        }
        Business::where('user_id', $subscription['user_id'])
            ->update(array('is_featured' => $is_featured,
            'featured_at' => $featured_at));
    }

    /**
     * Generic Unit Functions below
     */

    public function days_count_from($created_at)
    {
        # code...
        $end_date = Carbon::parse($created_at)->addMonth(1);
        $current_date = new DateTime();
        $interval = $current_date->diff($end_date);
        $days = $interval->format('%a');
        if ($end_date > $current_date) {
            $days = "-" . $days;
        } else {
            $days = "+" . $days;
        }
        return $days;
        
    }

    public function hours_count_from($created_at)
    {
        # code...
        $end_date = Carbon::parse($created_at)->addHour(1);
        $current_date = new DateTime();
        $interval = $current_date->diff($end_date);
        $days = $interval->format('%a');
        if ($end_date > $current_date) {
            $days = "-" . $days;
        } else {
            $days = "+" . $days;
        }
        return $days;
    }


    public function replace_zero_with_empty_string($subscription)
    {
        # code...
        foreach ($subscription as $key => $value) {
            # code...
            if ($value == '0') {
                $subscription[$key] = '';
            }
        }

        return $subscription;
    }

    public function get_subscription_default_array()
    {
        # code...
        $subscription = [];
        foreach ($this->subscription_array as $key) {
            # code...
            $subscription[$key] = '';
        }
        $subscription['status'] = false;
        return $subscription;
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
}
