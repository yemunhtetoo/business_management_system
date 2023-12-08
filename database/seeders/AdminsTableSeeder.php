<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('123456');
        $adminRecords = [
            ['id'=>2,'name'=>'April','type'=>'subadmin','mobile'=>9446993052,'email'=>'april@subadmin.com','password'=>$password,'image'=>'','status'=>1],
            ['id'=>3,'name'=>'Meetoke','type'=>'subadmin','mobile'=>9446993052,'email'=>'meetoke@subadmin.com','password'=>$password,'image'=>'','status'=>1],
        ];
        Admin::insert($adminRecords);
    }
}
