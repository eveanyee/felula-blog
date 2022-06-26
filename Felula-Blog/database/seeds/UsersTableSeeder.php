<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'role_id' => '1',
            'name' => 'MD.Admin',
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'password' => bcrypt('rootadmin'),
            'remember_token' => str_random(10),
            'created_at' => '2022-05-10',
            'updated_at' => '2022-05-10',


        ]);

        DB::table('users')->insert([
            'role_id' => '2',
            'name' => 'MD.Author',
            'username' => 'author',
            'email' => 'author@blog.com',
            'password' => bcrypt('rootauthor'),
            'remember_token' => str_random(10),
            'created_at' => '2022-05-10',
            'updated_at' => '2022-05-10',
        ]);
    }
}
