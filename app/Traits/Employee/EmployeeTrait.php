<?php

namespace App\Traits\Employee;


use App\Employee;
use App\PermissionGroup;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;


trait EmployeeTrait
{

  /**
   * @param Request $request
   * @return $this|false|string
   */

  public function employeeIndex(Request $request)
  {

    if ($request->ajax()) {
      return DataTables::of(Employee::all())
        ->addIndexColumn()
        ->addColumn('roleAndPermission', function ($row) {
          if ($row->User) {
            $roleAndPermission = '';
            foreach ($row->User->roles as $role) {
              $roleAndPermission .= str_replace('-', ' ', strtoupper($role->name)) . ', ';
            }
            foreach ($row->User->permissions as $permission) {
              $roleAndPermission .= str_replace('-', ' ', strtoupper($permission->name)) . ', ';
            }
          } else {
            $roleAndPermission = 'Login Not Created';
          }
          return $roleAndPermission;
        })
        ->addColumn('login', function ($row) {
          if ($row->User) {
            if ($row->User->login_revoked) {
              $loginAccessButton = '<button type = "button" class="btn btn-icon btn-warning mr-1 mb-1" onclick = "event.preventDefault(); document.getElementById(\'frm-activate-id\').value =  ' . $row->id . '; document.getElementById(\'frm-activate\').submit();" data - toggle = "popover"
                                               data - content = "{{ $row->name }}\'s login credentials are created but login is suspended. Click on this button to Activate the login again"
                                               data - trigger = "hover" data - original - title = "Activate Login" ><i
                                           class="feather icon-refresh-ccw" ></i >
                                       </button >';
            } else {
              $loginAccessButton = '<button type = "button" class="btn btn-icon btn-danger mr-1 mb-1" onclick = "event.preventDefault(); document.getElementById(\'frm-suspend-id\').value = ' . $row->id . '; document.getElementById(\'frm-suspend\').submit();" data - toggle = "popover"
                                               data - content = "Suspending Login will revoke {{ $row->name }}\'s login privilege. You can re-active the login later."
                                               data - trigger = "hover" data - original - title = "Suspend Login" ><i
                                           class="feather icon-x-circle" ></i >
                                       </button >';
            }
          } else {
            $loginAccessButton = '<button type = "button" class="btn btn-icon btn-success mr-1 mb-1"  onclick = "event.preventDefault(); document.getElementById(\'frm-create-id\').value = ' . $row->id . '; document.getElementById(\'frm-create\').submit();" data - toggle = "popover"
                                             data - content = "Create {{ $row->name }}\'s Login Credentials"
                                             data - trigger = "hover" data - original - title = "Create Login" ><i
                                         class="feather icon-plus" ></i >
                                     </button >';
          }
          $editButton = '<a href="' . route('edit.employee', ['id' => $row->id]) . '"><button type="button" class="btn btn-icon btn-black mr-1 mb-1"><i
                                         class="feather icon-edit"></i>
                                     </button></a>';


          return $loginAccessButton . $editButton;
        })
        ->rawColumns(['login'])
        ->make(true);
    }

    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['name' => "Employee"]
    ];
    return view('/employee/index', [
      'breadcrumbs' => $breadcrumbs
    ])->with('employees', Employee::all()->count());
  }

  public function createEmployeeForm()
  {

    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['link' => '/employee', 'name' => "Employee"], ['name' => 'Create New Employee']
    ];
    toast('Info Toast', 'info');
    return view('/settings/create-role', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function createEmployee(Request $request)
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

  public function userLogs()
  {
    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['name' => "Logs"]
    ];
    return view('/settings/user-log', [
      'breadcrumbs' => $breadcrumbs
    ])->with('logs', Auth::user()->LoginLog->sortBy('time')->reverse());
  }

  public function editEmployee($id)
  {
    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Settings"], ['link' => '/employee', 'name' => "Employee"], ['name' => 'Edit']
    ];
    return view('/employee/edit', [
      'breadcrumbs' => $breadcrumbs
    ])->with('employee', Employee::findOrFail($id));
  }

  public function updateEmployee(Request $request, $id)
  {
    $request->validate([
      'emplName' => 'required|string',
      'emplPhone' => 'required|integer',
      'emplEmail' => 'required|email',
      'emplDepartment' => 'required|string',
    ]);
    $employee = Employee::findOrFail($id);
    $employee->update([
      'name' => $request->emplName,
      'phone' => $request->emplPhone,
      'email' => $request->emplEmail,
      'department' => $request->emplDepartment
    ]);
    $employee->User ? $employee->User->update(['email' => $request->emplEmail]) : '';
    if ($request->roles) {
      $r = [];
      foreach ($request->roles as $role) {
        if (Role::findByName($role)) {
          array_push($r, $role);
        }
      }
      $employee->User->syncRoles($r);
    }
    $permissions = [];
    foreach (PermissionGroup::all() as $group) {
      foreach (explode(',', $group->group_permissions) as $permission) {
        if ($request->has($permission)) {
          array_push($permissions, $permission);
        }
      }
    }
    $employee->User->syncPermissions($permissions);
    notifyToast('success', 'Updated', $employee->name . '\'s Data Updated');
    return redirect()->route('employee');
  }

  public function suspendLogin(Request $request)
  {
    $employee = Employee::findOrFail($request->employeeId);
    if (Auth::user()->employee->id != $employee->id) {

      $employee->User()->update([
        'login_revoked' => 1
      ]);
      notifyToast('success', 'Login Revoked', $employee->name . '\'s Login Disabled Successfully');
    } else {
      notifyToast('error', 'OOPS!!', 'You can not suspend your own login');
    }
    return redirect()->back();
  }

  public function activateLogin(Request $request)
  {
    $employee = Employee::findOrFail($request->employeeId);
    $employee->User()->update([
      'login_revoked' => 0
    ]);
    notifyToast('success', 'Login Activated', $employee->name . '\'s Login Activated Successfully');
    return redirect()->back();
  }

  public function createLogin(Request $request)
  {
    $employee = Employee::findOrFail($request->employeeId);
    if (!User::where('email', $employee->email)->count()) {
      $employee->User()->create([
        'name' => $employee->name,
        'email' => $employee->email,
        'password' => Hash::make('password'),
      ]);
      notifyToast('success', 'Login Created', $employee->name . '\'s Login Created Successfully');
    } else {
      notifyToast('error', 'Duplicate Email', 'Login with email: ' . $employee->email . ' already exists, Please Update the email and try again');
    }
    return redirect()->back();
  }
}
