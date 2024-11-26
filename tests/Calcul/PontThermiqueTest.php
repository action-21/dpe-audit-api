<?php

namespace App\Tests\Calcul;

use App\Domain\PontThermique\Enum\{TypeIsolation, TypeLiaison, TypePose};
use App\Domain\PontThermique\Service\{MoteurPerformance};
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class PontThermiqueTest extends KernelTestCase
{
    #[DataProvider('kptTestsProvider')]
    public function testKpt(
        string $type_liaison,
        ?string $type_isolation_mur,
        ?string $type_isolation_plancher,
        ?string $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
        ?float $kpt_saisi,
        float $kpt,
    ): void {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEquals($moteur->kpt(
            type_liaison: TypeLiaison::from($type_liaison),
            type_isolation_mur: $type_isolation_mur ? TypeIsolation::from($type_isolation_mur) : null,
            type_isolation_plancher: $type_isolation_plancher ? TypeIsolation::from($type_isolation_plancher) : null,
            type_pose_ouverture: $type_pose_ouverture ? TypePose::from($type_pose_ouverture) : null,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            kpt_saisi: $kpt_saisi,
        ), $kpt);
    }

    #[DataProvider('ptTestsProvider')]
    public function testPt(float $longueur, bool $pont_thermique_partiel, float $kpt, float $pt): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEquals($moteur->pt(
            longueur: $longueur,
            pont_thermique_partiel: $pont_thermique_partiel,
            kpt: $kpt,
        ), $pt);
    }

    public static function kptTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/pont_thermique.yaml')['performance']['kpt'];
    }

    public static function ptTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/pont_thermique.yaml')['performance']['pt'];
    }
}
