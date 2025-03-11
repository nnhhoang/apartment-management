<?php

namespace App\Console\Commands;

use App\Events\UnpaidRentNotificationSent;
use App\Mail\UnpaidRentNotification;
use App\Models\RoomFeeCollection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUnpaidRentNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rent:unpaid-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email notifications for unpaid rent from previous month';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $previousMonth = Carbon::now()->subMonth();
        $this->info('Checking unpaid rent for ' . $previousMonth->format('F Y'));

        $users = User::whereHas('apartments.rooms')->get();
        
        $totalNotified = 0;
        
        foreach ($users as $user) {
            $unpaidCollections = RoomFeeCollection::with(['room.apartment', 'tenant', 'contract'])
                ->whereHas('room.apartment', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereMonth('charge_date', $previousMonth->month)
                ->whereYear('charge_date', $previousMonth->year)
                ->whereRaw('total_paid < total_price')
                ->get();
                
            if ($unpaidCollections->isNotEmpty()) {
                $this->info("User {$user->email} has {$unpaidCollections->count()} unpaid rooms");
                
                Mail::to($user)->send(new UnpaidRentNotification($user, $unpaidCollections, $previousMonth));

                event(new UnpaidRentNotificationSent($user, $unpaidCollections));
                
                $totalNotified += $unpaidCollections->count();
            }
        }
        
        $this->info("Notifications sent for {$totalNotified} unpaid rooms");
        
        return self::SUCCESS;
    }
}