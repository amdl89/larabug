<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(UserRole::getValues())]
        ]);

        $user->syncRoles($request->get('role'));

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'User Role Updated Successfully!'
        ]);

        return back();
    }
}
