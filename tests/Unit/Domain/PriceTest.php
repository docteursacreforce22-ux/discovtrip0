<?php
namespace Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use App\Domain\Offer\ValueObjects\Price;

class PriceTest extends TestCase {
    public function test_price_can_be_created(): void {
        $price = new Price(50000, 'XOF');
        $this->assertEquals(50000, $price->getAmount());
        $this->assertEquals('XOF', $price->getCurrency());
    }

    public function test_price_formats_correctly(): void {
        $priceXOF = new Price(50000, 'XOF');
        $priceEUR = new Price(100, 'EUR');

        $this->assertStringContainsString('50', $priceXOF->format());
        $this->assertStringContainsString('€', $priceEUR->format());
    }

    public function test_price_discount_calculation(): void {
        $price = new Price(100, 'EUR');
        $discounted = $price->applyDiscount(20);
        $this->assertEquals(80, $discounted->getAmount());
    }

    public function test_price_comparison(): void {
        $price1 = new Price(100, 'EUR');
        $price2 = new Price(150, 'EUR');

        $this->assertTrue($price2->greaterThan($price1));
        $this->assertTrue($price1->lessThan($price2));
    }
}
