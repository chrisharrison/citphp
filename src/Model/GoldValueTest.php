<?php

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use PHPUnit\Framework\TestCase;

final class GoldValueTest extends TestCase
{
    public function test_isMoreThan_returns_true_when_other_value_is_greater()
    {
        $test1 = new GoldValue(3);
        $test2 = new GoldValue(2);

        $this->assertTrue($test1->isMoreThan($test2));
    }

    public function test_isMoreThan_returns_false_when_other_value_is_less()
    {
        $test1 = new GoldValue(1);
        $test2 = new GoldValue(2);

        $this->assertFalse($test1->isMoreThan($test2));
    }

    public function test_isLessThan_returns_false_when_other_value_is_greater()
    {
        $test1 = new GoldValue(3);
        $test2 = new GoldValue(2);

        $this->assertFalse($test1->isLessThan($test2));
    }

    public function test_isLessThan_returns_true_when_other_value_is_less()
    {
        $test1 = new GoldValue(1);
        $test2 = new GoldValue(2);

        $this->assertTrue($test1->isLessThan($test2));
    }

    public function test_withIncrement_returns_new_value_that_is_incremented()
    {
        $test1 = new GoldValue(10);
        $test2 = new GoldValue(5);

        $calculated = $test1->withIncrement($test2);

        $this->assertEquals(15, $calculated->toNative());
    }

    public function test_withDecrement_returns_new_value_that_is_decremented()
    {
        $test1 = new GoldValue(10);
        $test2 = new GoldValue(5);

        $calculated = $test1->withDecrement($test2);

        $this->assertEquals(5, $calculated->toNative());
    }
}
