<?php

namespace App\Tests\Calcul;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Refroidissement\Service\MoteurPerformance;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class RefroidissementTest extends KernelTestCase
{
    #[DataProvider('eerProvider')]
    public function testEer(string $zone_climatique, int $annee_installation_generateur, ?float $seer_saisi, float $eer,): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEquals($moteur->eer(
            zone_climatique: ZoneClimatique::from($zone_climatique),
            annee_installation_generateur: $annee_installation_generateur,
            seer_saisi: $seer_saisi,
        ), $eer);
    }

    public static function eerProvider(): array
    {
        return Yaml::parseFile('etc/calculs/refroidissement.yaml')['performance']['eer'];
    }
}
