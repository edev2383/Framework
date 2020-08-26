<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Edev\System\Helpers\Arr;

final class EmployeeTest extends TestCase
{

    public function testEmployeeModelAllCanAcquireValues(): void
    {
        $this->assertNotEmpty(\Edev\Model\Employee::all());
    }

    public function testEmployeeModelWhereWorksAsExpected(): void
    {
        $this->assertNotEmpty(\Edev\Model\Employee::where('id', 1)->get());
    }
}