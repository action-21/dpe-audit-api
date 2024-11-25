<?php

namespace App\Tests\Calcul;

use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};
use App\Domain\Ventilation\Service\MoteurPerformance;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

final class VentilationTest extends KernelTestCase
{
    #[DataProvider('debitProvider')]
    public function testPerformance(
        string $type_systeme,
        ?string $mode_extraction,
        ?string $mode_insufflation,
        ?bool $presence_echangeur,
        ?bool $systeme_collectif,
        int $annee_installation,
        float $qvarep_conv,
        float $qvasouf_conv,
        float $smea_conv,
    ): void {
        self::bootKernel();
        $container = static::getContainer();
        /** @var MoteurPerformance */
        $moteur = $container->get(MoteurPerformance::class);

        $tests = Yaml::parseFile('etc/calculs/ventilation.yaml')['performance'];

        foreach ($tests['debit'] as $test) {
            $debit = $moteur->debit(
                type_systeme: TypeSysteme::from($type_systeme),
                mode_extraction: $mode_extraction ? ModeExtraction::from($mode_extraction) : null,
                mode_insufflation: $mode_insufflation ? ModeInsufflation::from($mode_insufflation) : null,
                presence_echangeur: $presence_echangeur,
                systeme_collectif: $systeme_collectif,
                annee_installation: $annee_installation,
            );
            $this->assertEquals($debit['qvarep_conv'], $qvarep_conv);
            $this->assertEquals($debit['qvasouf_conv'], $qvasouf_conv);
            $this->assertEquals($debit['smea_conv'], $smea_conv);
        }
    }

    public static function debitProvider(): array
    {
        return Yaml::parseFile('etc/calculs/ventilation.yaml')['performance']['debit'];
    }
}
