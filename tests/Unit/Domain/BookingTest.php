<?php
namespace Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use App\Domain\Booking\{Booking, BookingStatus, CancellationPolicy};
use App\Domain\Booking\ValueObjects\ParticipantInfo;
use App\Domain\Offer\ValueObjects\Price;

class BookingTest extends TestCase {
    public function test_booking_can_be_created_with_valid_data(): void {
        $booking = new Booking(
            userId: 1,
            offerId: 1,
            bookingDate: new \DateTime('+1 day'),
            participants: new ParticipantInfo(2, 1, 0),
            totalPrice: new Price(50000, 'XOF'),
            cancellationPolicy: CancellationPolicy::MODERATE
        );

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals(BookingStatus::PENDING, $booking->getStatus());
    }

    public function test_booking_cannot_be_created_with_past_date(): void {
        $this->expectException(\InvalidArgumentException::class);

        new Booking(
            userId: 1,
            offerId: 1,
            bookingDate: new \DateTime('-1 day'),
            participants: new ParticipantInfo(2),
            totalPrice: new Price(50000, 'XOF'),
            cancellationPolicy: CancellationPolicy::MODERATE
        );
    }

    public function test_booking_reference_is_generated_automatically(): void {
        $booking = new Booking(
            userId: 1,
            offerId: 1,
            bookingDate: new \DateTime('+1 day'),
            participants: new ParticipantInfo(2),
            totalPrice: new Price(50000, 'XOF'),
            cancellationPolicy: CancellationPolicy::MODERATE
        );

        $reference = $booking->getReference();
        $this->assertMatchesRegularExpression('/^BK-\d{8}-[A-Z0-9]{5}$/', $reference->getValue());
    }

    public function test_booking_can_be_confirmed_after_payment(): void {
        $booking = new Booking(
            userId: 1,
            offerId: 1,
            bookingDate: new \DateTime('+1 day'),
            participants: new ParticipantInfo(2),
            totalPrice: new Price(50000, 'XOF'),
            cancellationPolicy: CancellationPolicy::MODERATE
        );

        $booking->markAsPaid(1, 'stripe');
        $booking->confirm();

        $this->assertEquals(BookingStatus::CONFIRMED, $booking->getStatus());
        $this->assertTrue($booking->isPaid());
    }
}
