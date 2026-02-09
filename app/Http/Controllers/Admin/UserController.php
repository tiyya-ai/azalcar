<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $role = $request->role;
            if ($role === 'seller_pending') {
                $query->where('seller_status', 'pending');
            } elseif ($role === 'seller') {
                $query->where(function($q) {
                    $q->where('role', 'vendor')->orWhere('seller_status', 'approved');
                });
            } else {
                $query->where('role', $role);
            }
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(15);
        $pendingSellersCount = User::where('seller_status', 'pending')->count();

        return view('admin.users.index', compact('users', 'pendingSellersCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $listings = $user->listings()->latest()->take(5)->get();
        $transactions = $user->transactions()->latest()->take(5)->get();
        return view('admin.users.show', compact('user', 'listings', 'transactions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,vendor,user',
            'status' => 'required|in:active,banned',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('name', 'email', 'phone', 'role', 'status');

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                \Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->is_admin && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin.');
        }

        $user->delete(); // Soft delete
        return back()->with('success', 'User deleted successfully.');
    }

    // Custom Actions

    public function approveSeller(User $user)
    {
        $user->update([
            'seller_status' => 'approved',
            'role' => 'vendor', // Promote to vendor role
            'seller_approved_at' => now()
        ]);

        // Send notification to the user
        $user->notify(new \App\Notifications\SellerApproved());

        return back()->with('success', 'Seller approved successfully.');
    }

    public function rejectSeller(User $user)
    {
        $user->update([
            'seller_status' => 'rejected'
        ]);

        // Send notification to the user
        $user->notify(new \App\Notifications\SellerRejected());

        return back()->with('success', 'Seller application rejected.');
    }
    
    public function ban(Request $request, User $user)
    {
        $user->update([
            'status' => 'banned',
            'ban_reason' => $request->ban_reason
        ]);
        
        return back()->with('success', 'User has been banned.');
    }
}
