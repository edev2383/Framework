<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ClientGlobalTest extends TestCase
{
    public function testCannotRecieveANonsenseClientSettingsContant(): void
    {
        // Manager class creates the database connects for the Model classes
        new \Edev\Database\Manager\Manager();
        $this->expectException(\Exception\GlobalClientValueNotFound::class);
        \Edev\Model\Meta\ClientGlobal::get('invalid');
    }

    public function testClientSettingsReturnsAnArray(): void
    {
        // Manager class creates the database connects for the Model classes
        new \Edev\Database\Manager\Manager();

        $constant = 'system_announce_prefix';

        $this->assertEquals(
            'array',
<<<<<<< HEAD
            gettype(\Model\Meta\ClientGlobal::get($constant))
=======
            gettype(\Edev\Model\Meta\ClientGlobal::get($constant))
>>>>>>> dfb347c3932d8521dd2e047475ab2ff5db545354
        );
    }
}
