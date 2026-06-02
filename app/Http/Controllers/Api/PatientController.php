<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('role')
            ->whereHas('role', function ($q) {
                $q->where('name', 'patient');
            });

        if ($request->filled('name')) {
            $query->where(
                'name',
                'like',
                '%' . $request->name . '%'
            );
        }

        if ($request->filled('email')) {
            $query->where(
                'email',
                'like',
                '%' . $request->email . '%'
            );
        }

        if ($request->filled('phone')) {
            $query->where(
                'phone',
                'like',
                '%' . $request->phone . '%'
            );
        }

        $patients = $query
            ->select(
                'id',
                'role_id',
                'name',
                'email',
                'phone',
                'status',
                'created_at'
            )
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }
}
