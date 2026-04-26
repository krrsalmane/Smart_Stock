<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleManagementController extends Controller
{
    const VALID_ROLES = ['admin', 'magasinier', 'client', 'supplier', 'delivery_agent'];
    
    const MAGASINIER_ASSIGNABLE_ROLES = ['delivery_agent', 'supplier'];

    public function getAvailableRoles()
    {
        $existingRoles = User::distinct()->pluck('role')->toArray();
        
        $allRoles = collect(self::VALID_ROLES)->map(function ($role) {
            return [
                'role' => $role,
                'label' => $this->getRoleLabel($role),
                'exists' => false,
            ];
        });

        $allRoles = $allRoles->map(function ($roleData) use ($existingRoles) {
            $roleData['exists'] = in_array($roleData['role'], $existingRoles);
            return $roleData;
        });

        return response()->json([
            'roles' => $allRoles,
            'all_exist' => $allRoles->every(fn($r) => $r['exists']),
        ]);
    }

    public function assignRole(Request $request, $userId)
    {
        $user = auth()->user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:' . implode(',', self::VALID_ROLES),
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $requestedRole = $request->role;

        if ($user->role === 'admin') {
            // Admin can assign any role
        } elseif ($user->role === 'magasinier') {
            // Magasinier can only assign delivery_agent or supplier
            if (!in_array($requestedRole, self::MAGASINIER_ASSIGNABLE_ROLES)) {
                return response()->json([
                    'error' => 'Unauthorized. Magasiniers can only assign delivery_agent or supplier roles.',
                ], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->id === $targetUser->id) {
            return response()->json(['error' => 'Cannot change your own role'], 400);
        }

        $targetUser->update(['role' => $requestedRole]);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $targetUser,
        ]);
    }

    public function getAssignableRoles()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $roles = self::VALID_ROLES;
        } elseif ($user->role === 'magasinier') {
            $roles = self::MAGASINIER_ASSIGNABLE_ROLES;
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'roles' => collect($roles)->map(fn($role) => [
                'role' => $role,
                'label' => $this->getRoleLabel($role),
            ])->values(),
        ]);
    }

    public function getAllUsersWithRoles()
    {
        $user = auth()->user();
        
        $query = User::select('id', 'name', 'email', 'role', 'created_at');

        // Admin sees ALL users including other admins and magasiniers
        // Magasinier sees limited view (clients, suppliers, delivery agents)
        if ($user->role === 'magasinier') {
            $query->whereIn('role', ['client', 'supplier', 'delivery_agent']);
        }

        $users = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    private function getRoleLabel($role)
    {
        $labels = [
            'admin' => 'Administrator',
            'magasinier' => 'Magasinier (Stock Manager)',
            'client' => 'Client',
            'supplier' => 'Supplier',
            'delivery_agent' => 'Delivery Agent',
        ];

        return $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }
}
