<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('is_admin', false)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', '회원이 삭제되었습니다.');
    }
}
