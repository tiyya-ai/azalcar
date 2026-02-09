<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired reservations and forfeit deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired reservations...');

        $expiredReservations = Reservation::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredReservations->isEmpty()) {
            $this->info('No expired reservations found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredReservations as $reservation) {
            DB::beginTransaction();
            try {
                $this->info("Processing expired reservation #{$reservation->id}");
                
                // Forfeit deposit (50/50 split)
                $shares = $reservation->forfeitDeposit();
                
                // Credit Seller
                if ($reservation->seller) {
                    if (!$reservation->seller->updateBalance($shares['seller_share'], 'add')) {
                        $this->error("Failed to credit seller #{$reservation->seller_id} for expired reservation #{$reservation->id}");
                        continue;
                    }
                    
                    \App\Models\Transaction::create([
                        'user_id' => $reservation->seller_id,
                        'listing_id' => $reservation->listing_id,
                        'amount' => $shares['seller_share'],
                        'type' => 'deposit_forfeiture',
                        'description' => "Deposit forfeiture share (50%) from expired reservation #{$reservation->id}",
                        'status' => 'completed',
                    ]);
                }
                
                // Log platform earning
                Log::info("Platform earned {$shares['website_share']} from expired reservation #{$reservation->id}");

                DB::commit();
                $count++;
                
                // Future: Send notification to user and seller here
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to process expired reservation #{$reservation->id}: " . $e->getMessage());
                $this->error("Failed to process reservation #{$reservation->id}");
            }
        }

        $this->info("Successfully processed {$count} expired reservations.");
        return 0;
    }
}
