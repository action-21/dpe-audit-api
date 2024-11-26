<?php

namespace App\Tests\Calcul;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Eclairage\Service\{MoteurConsommation};
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class EclairageTest extends KernelTestCase
{
    #[DataProvider('ceclTestsProvider')]
    public function testCecl(string $zone_climatique, float $cecl): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurConsommation */
        $moteur = $container->get(MoteurConsommation::class);

        $this->assertEqualsWithDelta($moteur->cecl(
            zone_climatique: ZoneClimatique::from($zone_climatique)
        ), $cecl, 0.0001);
    }

    public static function ceclTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/eclairage.yaml')['consommation']['cecl'];
    }
}
