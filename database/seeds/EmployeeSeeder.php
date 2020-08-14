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
      $user = $employee->user()->create([
        'name' => $employee->name,
        'email' => $employee->email,
        'password' => Hash::make('password'),
      ]);
      $user->assignRole(\Spatie\Permission\Models\Role::findByName('super-admin'));


//            $faker = Faker\Factory::create();
//      foreach (range(1,36) as $index) {
//        \App\Employee::create([
//          'name'=>$faker->name,
//          'email'=>$faker->email,
//          'phone'=>$faker->e164PhoneNumber,
//          'department'=>'IT',
//        ]);
//      }

    }
}
