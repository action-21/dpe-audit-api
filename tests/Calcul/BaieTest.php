<?php

namespace App\Tests\Calcul;

use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Baie\Service\MoteurEnsoleillement;
use App\Domain\Common\Enum\Orientation;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class BaieTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    #[DataProvider('fe1TestsProvider')]
    public function testFe1(string $type_masque_proche, ?string $orientation_baie, ?float $avancee_masque, float $fe1): void
    {
        /** @var MoteurEnsoleillement */
        $moteur = static::getContainer()->get(MoteurEnsoleillement::class);

        $this->assertEqualsWithDelta($moteur->fe1(
            type_masque_proche: TypeMasqueProche::from($type_masque_proche),
            avancee_masque: $avancee_masque,
            orientation_baie: $orientation_baie ? Orientation::from($orientation_baie) : null
        ), $fe1, 0.0001);
    }

    #[DataProvider('fe2TestsProvider')]
    public function testFe2(string $type_masque_lointain, string $orientation_baie, float $hauteur_masque_alpha, float $fe2): void
    {
        /** @var MoteurEnsoleillement */
        $moteur = static::getContainer()->get(MoteurEnsoleillement::class);

        $this->assertEqualsWithDelta($moteur->fe2(
            type_masque_lointain: TypeMasqueLointain::from($type_masque_lointain),
            orientation_baie: Orientation::from($orientation_baie),
            hauteur_masque: $hauteur_masque_alpha,
        ), $fe2, 0.0001);
    }

    public static function fe1TestsProvider(): array
    {
        return Yaml::parseFile("etc/calculs/baie.yaml")['ensoleillement']['fe1'];
    }

    public static function fe2TestsProvider(): array
    {
        return Yaml::parseFile("etc/calculs/baie.yaml")['ensoleillement']['fe2'];
    }
}
