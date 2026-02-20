<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = array(
            array('business_id' => 1, 'email' => 'shopowner@acnoo.com', 'name' => 'Acnoo', 'role' => 'shop-owner', 'phone' => '3452534', 'image' => NULL, 'lang' => 'en', 'visibility' => NULL, 'password' => bcrypt('123456'), 'status' => '1', 'email_verified_at' => NULL, 'remember_token' => NULL, 'created_at' => '2024-09-26 12:12:22', 'updated_at' => '2024-09-26 12:12:22'),
            array('business_id' => NULL, 'email' => 'admin@admin.com', 'name' => 'Super Admin', 'role' => 'superadmin', 'phone' => '1234567890', 'image' => NULL, 'lang' => 'en', 'visibility' => NULL, 'password' => bcrypt('admin'), 'status' => '1', 'email_verified_at' => now(), 'remember_token' => NULL, 'created_at' => now(), 'updated_at' => now()),
        );

        User::insert($users);
    }
}
