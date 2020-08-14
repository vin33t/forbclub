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
//      Role Permissions
    \App\PermissionGroup::create([
      'group_name' => 'Role',
      'group_permissions' => 'view-role,update-role,create-role,delete-role'
    ]);
    \App\PermissionGroup::create([
      'group_name' => 'Employee',
      'group_permissions' => 'view-employee,update-employee,create-employee,delete-employee'
    ]);
    $superAdmin = \Spatie\Permission\Models\Role::create(['guard_name' => 'web', 'name' => 'super-admin']);
    foreach (\App\PermissionGroup::all() as $group){
      foreach (explode(',', $group->group_permissions) as $permission) {
       \Spatie\Permission\Models\Permission::create(['guard_name' => 'web', 'name' => $permission]);
       $superAdmin->givePermissionTo($permission);
      }
  }

    }
}
