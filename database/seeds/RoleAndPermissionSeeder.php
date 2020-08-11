<?php

use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      \Spatie\Permission\Models\Permission::create(['guard_name' => 'web', 'name' => 'create-role']);
      \Spatie\Permission\Models\Permission::create(['guard_name' => 'web', 'name' => 'view-role']);
      \Spatie\Permission\Models\Permission::create(['guard_name' => 'web', 'name' => 'update-role']);
      \Spatie\Permission\Models\Permission::create(['guard_name' => 'web', 'name' => 'delete-role']);
    }
}
