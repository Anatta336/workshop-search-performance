<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $totalToCreate = 2000;

        $faker = Faker::create();
        $now = now();
        $chunkSize = 1000;
        $rows = [];

        for ($i = 0; $i < $totalToCreate; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            $email = strtolower($firstName).'.'.strtolower($lastName).'.'.$i.'@example.com';

            $rows[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $faker->phoneNumber(),
                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'postal_code' => $faker->postcode(),
                'country' => $faker->country(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= $chunkSize) {
                DB::table('customers')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('customers')->insert($rows);
        }
    }
}
