<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions.
     */
    public function index(Request $request)
    {
        $query = Commission::with(['listing', 'seller']);

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate totals
        $totalPending = Commission::where('status', 'pending')->sum('final_commission');
        $totalPaid = Commission::where('status', 'paid')->sum('final_commission');
        $totalWaived = Commission::where('status', 'waived')->sum('final_commission');

        return view('admin.commissions.index', compact('commissions', 'totalPending', 'totalPaid', 'totalWaived'));
    }

    /**
     * Display the specified commission.
     */
    public function show(Commission $commission)
    {
        $commission->load(['listing', 'seller', 'transaction']);
        return view('admin.commissions.show', compact('commission'));
    }

    /**
     * Mark commission as paid manually.
     */
    public function markAsPaid(Commission $commission)
    {
        if ($commission->status === 'paid') {
            return back()->with('error', 'Commission is already paid.');
        }

        $commission->markAsPaid();

        return back()->with('success', 'Commission marked as paid successfully.');
    }

    /**
     * Waive a commission.
     * Creates audit log entry for compliance tracking
     */
    public function waive(Commission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be waived.');
        }

        $commission->update([
            'status' => 'waived',
            'notes' => ($commission->notes ?? '') . "\nWaived by admin #" . auth()->id() . " on " . now()->toDateTimeString()
        ]);

        // Create audit log entry
        \App\Models\AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action_type' => 'commission_waived',
            'target_type' => 'Commission',
            'target_id' => $commission->id,
            'description' => "Waived commission #{$commission->id}. Amount: {$commission->final_commission} RUB. Seller: #{$commission->seller_id}. Listing: #{$commission->listing_id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => json_encode([
                'commission_id' => $commission->id,
                'seller_id' => $commission->seller_id,
                'listing_id' => $commission->listing_id,
                'amount' => $commission->final_commission,
                'reason' => 'Manual waiver by admin'
            ])
        ]);

        \Illuminate\Support\Facades\Log::info('Commission waived', [
            'commission_id' => $commission->id,
            'seller_id' => $commission->seller_id,
            'amount' => $commission->final_commission,
            'admin_id' => auth()->id()
        ]);

        return back()->with('success', 'Commission waived successfully.');
    }
}
