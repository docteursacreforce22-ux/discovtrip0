<?php
namespace Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use App\Domain\Booking\ValueObjects\ParticipantInfo;

class ParticipantInfoTest extends TestCase {
    public function test_participant_info_can_be_created(): void {
        $participants = new ParticipantInfo(2, 1, 0);

        $this->assertEquals(2, $participants->getAdults());
        $this->assertEquals(1, $participants->getChildren());
        $this->assertEquals(0, $participants->getInfants());
        $this->assertEquals(3, $participants->getTotal());
    }

    public function test_at_least_one_adult_is_required_with_children(): void {
        $this->expectException(\InvalidArgumentException::class);
        new ParticipantInfo(0, 2, 1);
    }

    public function test_participant_info_formats_correctly(): void {
        $participants = new ParticipantInfo(2, 1, 1);
        $this->assertEquals('2 adultes, 1 enfant, 1 bébé', $participants->format());
    }

    public function test_is_group_returns_true_for_4_plus_people(): void {
        $participants = new ParticipantInfo(3, 1, 0);
        $this->assertTrue($participants->isGroup());
    }

    public function test_is_family_group_detects_correctly(): void {
        $participants = new ParticipantInfo(2, 2, 0);
        $this->assertTrue($participants->isFamilyGroup());
    }
}
