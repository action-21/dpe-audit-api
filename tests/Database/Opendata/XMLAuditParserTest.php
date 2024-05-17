<?php

namespace App\Tests\Database\Opendata;

use App\Database\Opendata\Audit\XMLAuditParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class XMLAuditParserTest extends KernelTestCase
{
    public function testTest(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(XMLAuditParser::class);
        $this->assertNotNull(null);
    }
}
