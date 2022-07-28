<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'FirstNameTest',
            'last_name' => 'LastNameTest',
            'email' => 'testemail@mail.ru',
            'password' => '123123Abc',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('products')->insert([
            [
                'name' => 'tree',
                'place' => 'Spain',
                'implementation_time' => Carbon::create(2022),
                'price' => 39,
                'currency_code' => 'EUR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'name' => 'tree',
                'place' => 'Portugal',
                'implementation_time' => Carbon::create(2022),
                'price' => 39,
                'currency_code' => 'EUR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'tree',
                'place' => 'France',
                'implementation_time' => Carbon::create(2023),
                'price' => 39,
                'currency_code' => 'EUR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        DB::table('certificates')->insert([
            [
                'identity' => '3421A67F',
                'user_id' => 1,
                'status' => 'active',
                'total_price' => 39,
                'currency_code' => 'EUR',
                'product_id' => 1,
                'product_count' => 1,
                'activation_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'identity' => '1G11B679',
                'user_id' => 1,
                'status' => 'active',
                'total_price' => 39,
                'currency_code' => 'EUR',
                'product_id' => 1,
                'product_count' => 1,
                'activation_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
