<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee = \App\Employee::create([
          'name'=>'Vineet Chauhan',
          'email'=>'vineet.chd09@gmail.com',
          'phone'=>'8091004910',
          'department'=>'IT',
        ]);
        $employee->user()->create([
          'name' => $employee->name,
          'email' => $employee->email,
          'password' => Hash::make('password'),
        ]);
    }
}
