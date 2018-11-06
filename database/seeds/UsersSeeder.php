<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('cloudsecret'),
//            'birthday' => Carbon::parse('1990-01-01'),
//            'civic' => '1445',
//            'street_name' => 'Boulevard de Maisonneuve Ouest',
//            'city' => 'montreal',
//            'province' => 'quebec',
//            'postal_address' => 'h3g1m8',
//            'country' => 'canada',
        ]);
    }
}
