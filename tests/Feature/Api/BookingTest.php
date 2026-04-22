<?php
namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\{User, Offer, Booking};
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase {
    use RefreshDatabase;

    public function test_user_can_create_booking(): void {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/bookings', [
                             'offer_id' => $offer->id,
                             'booking_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                             'adults' => 2,
                             'children' => 1,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'data']);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'offer_id' => $offer->id,
        ]);
    }

    public function test_user_can_view_their_bookings(): void {
        $user = User::factory()->create();
        $bookings = Booking::factory(3)->create(['user_id' => $user->id]);
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/bookings');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_user_can_cancel_their_booking(): void {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson("/api/bookings/{$booking->id}/cancel", [
                             'reason' => 'Plans changed',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled_by_user',
        ]);
    }
}
