<?php

namespace App\Tests\Calcul;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Lnc\Enum\{Mitoyennete, NatureMenuiserie, TypeBaie, TypeLnc, TypeVitrage};
use App\Domain\Lnc\Service\{MoteurEnsoleillement, MoteurPerformance};
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class LncTest extends KernelTestCase
{
    #[DataProvider('uvueTestsProvider')]
    public function testUvue(string $type_local_non_chauffe, float $uvue): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEqualsWithDelta($moteur->uvue(
            type_lnc: TypeLnc::from($type_local_non_chauffe)
        ), $uvue, 0.0001);
    }

    #[DataProvider('bTestsProvider')]
    public function testB(float $uvue, float $aiu, float $aue, bool $isolation_aiu, bool $isolation_aue, float $b): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEqualsWithDelta($moteur->b(
            uvue: $uvue,
            aiu: $aiu,
            aue: $aue,
            isolation_aiu: $isolation_aiu,
            isolation_aue: $isolation_aue,
        ), $b, 0.0001);
    }

    #[DataProvider('bverTestsProvider')]
    public function testBver(string $zone_climatique, string $orientation, bool $isolation_paroi, float $bver): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $this->assertEqualsWithDelta($moteur->bver(
            zone_climatique: ZoneClimatique::from($zone_climatique),
            orientations: [Orientation::from($orientation)],
        )->bver(isolation_paroi: $isolation_paroi), $bver, 0.0001);
    }

    #[DataProvider('tTestsProvider')]
    public function testT(
        string $type_baie,
        ?string $nature_menuiserie,
        ?string $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
        float $t,
    ): void {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurEnsoleillement */
        $moteur = $container->get(MoteurEnsoleillement::class);

        $this->assertEqualsWithDelta($moteur->t(
            type_baie: TypeBaie::from($type_baie),
            nature_menuiserie: $nature_menuiserie ? NatureMenuiserie::from($nature_menuiserie) : null,
            type_vitrage: $type_vitrage ? TypeVitrage::from($type_vitrage) : null,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        ), $t, 0.0001);
    }

    public static function uvueTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/local_non_chauffe.yaml')['performance']['uvue'];
    }

    public static function bTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/local_non_chauffe.yaml')['performance']['b'];
    }

    public static function bverTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/local_non_chauffe.yaml')['performance']['bver'];
    }

    public static function tTestsProvider(): array
    {
        return Yaml::parseFile('etc/calculs/local_non_chauffe.yaml')['ensoleillement']['t'];
    }
}
