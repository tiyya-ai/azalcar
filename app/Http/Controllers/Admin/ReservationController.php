<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['listing', 'user', 'seller']);

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'active' => Reservation::where('status', 'active')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
            'expired' => Reservation::where('status', 'expired')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['listing', 'user', 'seller', 'transaction']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Manually expire a reservation.
     */
    public function markAsExpired(Reservation $reservation)
    {
        if ($reservation->status !== 'active') {
            return back()->with('error', 'Only active reservations can be expired.');
        }

        try {
            $reservation->update(['status' => 'expired']);
            // Note: In a real scenario, this should trigger the forfeiture logic
            // providing manual control here just in case of system failure
            
            // Trigger forfeiture logic if deposit exists
            if ($reservation->deposit_amount > 0 && !$reservation->deposit_forfeited) {
                $reservation->forfeitDeposit();
            }

            return back()->with('success', 'Reservation marked as expired and deposit forfeited.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error expiring reservation: ' . $e->getMessage());
        }
    }

    /**
     * Force cancel a reservation.
     */
    public function cancel(Reservation $reservation)
    {
        if ($reservation->status !== 'active') {
            return back()->with('error', 'Only active reservations can be cancelled.');
        }

        try {
            // Admin cancellation (force) triggers forfeiture
            $reservation->forfeitDeposit();
            
            return back()->with('success', 'Reservation cancelled and deposit forfeited.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error cancelling reservation: ' . $e->getMessage());
        }
    }
}
