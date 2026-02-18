<?php

use Illuminate\Database\Seeder;
use App\Models\Gateway;

class MoyasarGatewaySeeder extends Seeder
{
    public function run()
    {
        Gateway::updateOrCreate(
            ['namespace' => 'App\Library\Moyasar'],
            [
                'name' => 'Moyasar',
                'mode' => 'live',
                'data' => json_encode([
                    'publishable_key' => '',
                    'secret_key' => '',
                    'environment' => 'test'
                ]),
                'image' => 'moyasar.png',
                'status' => 1,
                'charge' => 0,
                'platform' => 'web',
                'is_manual' => 0,
                'accept_img' => 0,
                'phone_required' => 0,
                'currency_id' => 1, // SAR
                'instructions' => 'Moyasar Payment Gateway for Saudi Arabia'
            ]
        );
    }
}