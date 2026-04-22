<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Payment, Booking};

class PaymentSeeder extends Seeder {
    public function run(): void {
        $this->command->info('💳 Seeding payments...');
        
        $paidBookings = Booking::where('is_paid', true)->get();
        $gateways = ['stripe', 'fedapay'];
        $methods = ['card', 'mobile_money'];
        
        foreach ($paidBookings as $booking) {
            Payment::create([
                'transaction_id' => 'TXN-' . strtoupper(uniqid() . bin2hex(random_bytes(4))),
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'currency' => $booking->currency,
                'gateway' => $gateways[array_rand($gateways)],
                'method' => $methods[array_rand($methods)],
                'status' => 'succeeded',
                'gateway_payment_id' => 'ch_' . uniqid(),
                'created_at' => $booking->paid_at,
            ]);
        }
        
        $this->command->info('✅ Payments seeded: ' . Payment::count());
    }
}
