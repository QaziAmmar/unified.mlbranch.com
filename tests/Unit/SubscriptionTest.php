<?php

namespace Tests\Unit;

use Tests\TestCase;



class SubscriptionTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function testSubscriptionStatus()
    {
        // ($days, $is_subscribed)
        # code...
        $data = [
            'days' => -3,
            'is_subscribed' => true
        ];

        $response = $this->postJson('api/subscription/subscription_status', $data);
        
    }
}
