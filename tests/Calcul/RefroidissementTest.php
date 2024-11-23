<?php

namespace App\Tests\Calcul;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Refroidissement\Service\MoteurPerformanceGenerateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class RefroidissementTest extends KernelTestCase
{
    public function testPerformanceGenerateur(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformanceGenerateur */
        $moteur = $container->get(MoteurPerformanceGenerateur::class);

        $tests = Yaml::parseFile('etc/calculs/refroidissement.performance_generateur.yaml');

        foreach ($tests['eer'] as $test) {
            $resultat = $moteur->eer(
                zone_climatique: ZoneClimatique::from($test['zone_climatique']),
                annee_installation_generateur: $test['annee_installation_generateur'],
                seer_saisi: $test['seer_saisi'] ?? null,
            );
            $this->assertEquals($resultat, $test['resultat']);
        }
    }
}
