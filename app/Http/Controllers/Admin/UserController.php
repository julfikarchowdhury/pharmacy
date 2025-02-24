<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('role', 'user')->get();

            return DataTables::of($query)
                ->addColumn('image', function ($user) {
                    return '<img src="' . asset($user->image) . '" alt="User Image" class="img-thumbnail" height="50"height="100">';
                })
                ->addColumn('actions', function ($user) {
                    return '
                <a href="' . route('users.show', $user->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-info"></i>
                </a>
                <button class="btn btn-danger btn-sm" onclick="deleteUser(' . $user->id . ')">
                    <i class="fas fa-trash"></i>
                </button>
                ';
                })
                ->addColumn('status', function ($user) {
                    return $user->status == 'active'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changeStatus(' . $user->id . ')">Active</span>'
                        : '<span class="badge badge-danger" style="cursor: pointer;" onclick="changeStatus(' . $user->id . ')">Inactive</span>';
                })
                ->rawColumns(['image', 'actions', 'status'])
                ->make(true);
        }

        return view('admin.users.index');
    }
}
