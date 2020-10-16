<?php

namespace App\Traits\Settings;


use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

trait RoleManagementTrait
{

  /**
   * @param Request $request
   * @return $this|false|string
   */

  public function roleManagementIndex()
  {
    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['name' => "Role Management"]
    ];
    return view('/settings/role-management', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function createRoleForm()
  {

    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['link' => '/role-management', 'name' => "Role Management"], ['name' => 'Create New Role']
    ];
    toast('Info Toast', 'info');
    return view('/settings/create-role', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function createRole(Request $request)
  {
    $request->validate([
      'roleName' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/'
    ]);
    $role = Role::create(['name' => $request->roleName]);
    $permissions = $request->except(['_token', 'roleName']);
    foreach (array_keys($permissions) as $permission) {
      $role->givePermissionTo($permission);
    }
    notifyToast('success', 'Role Created', $role->name . ' Role has been Created Successfully');
    return redirect()->route('role-management');

  }

  public function convertRolesToPermissions($userId)
  {
    $user = User::find($userId);
    $permissions = [];
    foreach ($user->roles as $role) {
      foreach ($role->permissions as $permission) {
        array_push($permissions, $permission);
      }
      $user->removeRole($role);
    }
    $user->syncPermissions($permissions);
    notifyToast('success', 'Roles Converted', $role->name . ' Role has been Converted to Permissions Successfully');
    return redirect()->back();
  }

  public function convertPermissionsToRoles()
  {
    return 'p 2 r';
  }
}
