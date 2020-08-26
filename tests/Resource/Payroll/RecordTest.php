<?php

declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

final class RecordTest extends TestCase
{
    protected static $mockShift = [
        [
            'id' => 118,
            'shift_id' => 137,
            'employee_id' => 1,
            '_clock' => 1000,
            '_break' => 1000,
            '_lunch' => 100,
            '_paid' => 1000,
            '_reg' => 259956,
            '_ot' => 43859,
            '_rate' => 23.00,
            'pay_rate' => 23.00,
        ],
    ];

    public function testSummaryTotalPayIsCaculatedCorrectly(): void
    {
        $record = new \Edev\Resource\Payroll\Record(self::$mockShift, '');
        $this->assertEquals(2081.04, $record->getSummary()['totalPay']);
    }

    public function testSummaryTotalRegularPayIsCalculatedCorrectly(): void
    {
        $record = new \Edev\Resource\Payroll\Record(self::$mockShift, '');
        $this->assertEquals(1660.83, $record->getSummary()['totalRegularPay']);
    }

    public function testSummaryTotalOvertimePayIsCalculatedCorrectly(): void
    {
        $record = new \Edev\Resource\Payroll\Record(self::$mockShift, '');
        $this->assertEquals(420.21, $record->getSummary()['totalOvertimePay']);
    }
}
