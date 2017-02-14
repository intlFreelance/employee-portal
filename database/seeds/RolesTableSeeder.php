<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' =>'Administrator', 'description'=>'', 'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'employee', 'display_name' => 'Employee', 'description'=>'', 'created_at'=>$now, 'updated_at'=>$now]
        ]);
    }
}
