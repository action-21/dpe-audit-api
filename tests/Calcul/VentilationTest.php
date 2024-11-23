<?php

namespace App\Tests\Calcul;

use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\Service\MoteurPerformanceInstallation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class VentilationTest extends KernelTestCase
{
    public function testPerformance(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformanceInstallation */
        $moteur = $container->get(MoteurPerformanceInstallation::class);

        $tests = Yaml::parseFile('etc/calculs/ventilation.performance.yaml');

        foreach ($tests['qvarep_conv'] as $test) {
            $resultat = $moteur->qvarep_conv(
                type_ventilation: TypeVentilation::from($test['type_ventilation']),
                type_installation: $test['type_installation'] ? TypeInstallation::from($test['type_installation']) : null,
                presence_echangeur: $test['presence_echangeur'],
                presence_entree_air_hygroreglable: $test['presence_entree_air_hygroreglable'],
                annee_installation: $test['annee_installation'],
            );
            $this->assertEquals($resultat, $test['resultat']);
        }
        foreach ($tests['qvasouf_conv'] as $test) {
            $resultat = $moteur->qvasouf_conv(
                type_ventilation: TypeVentilation::from($test['type_ventilation']),
                type_installation: $test['type_installation'] ? TypeInstallation::from($test['type_installation']) : null,
                presence_echangeur: $test['presence_echangeur'],
                presence_entree_air_hygroreglable: $test['presence_entree_air_hygroreglable'],
                annee_installation: $test['annee_installation'],
            );
            $this->assertEquals($resultat, $test['resultat']);
        }
        foreach ($tests['smea_conv'] as $test) {
            $resultat = $moteur->smea_conv(
                type_ventilation: TypeVentilation::from($test['type_ventilation']),
                type_installation: $test['type_installation'] ? TypeInstallation::from($test['type_installation']) : null,
                presence_echangeur: $test['presence_echangeur'],
                presence_entree_air_hygroreglable: $test['presence_entree_air_hygroreglable'],
                annee_installation: $test['annee_installation'],
            );
            $this->assertEquals($resultat, $test['resultat']);
        }
    }
}
