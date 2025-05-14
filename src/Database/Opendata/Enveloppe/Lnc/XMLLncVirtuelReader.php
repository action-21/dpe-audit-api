<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\Enveloppe\Enum\Lnc\TypeLnc;

/**
 * Reconstitution des locaux non chauffés pour les parois qui ne sont pas associées à un espace tampon solarisé
 */
final class XMLLncVirtuelReader extends XMLParoiReader implements XMLLncReader
{
    public function description(): string
    {
        return 'Local non chauffé reconstitué';
    }

    public function surface(): float
    {
        return 0;
    }

    public function type(): TypeLnc
    {
        return TypeLnc::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function isolation_paroi_aue(): EtatIsolation
    {
        return match ($this->enum_cfg_isolation_lnc_id()) {
            2, 4 => EtatIsolation::NON_ISOLE,
            3, 5 => EtatIsolation::ISOLE,
            default => EtatIsolation::NON_ISOLE,
        };
    }

    public function isolation_paroi_aiu(): EtatIsolation
    {
        return match ($this->enum_cfg_isolation_lnc_id()) {
            2, 3, 9, 10, 11 => EtatIsolation::NON_ISOLE,
            4, 5, 6, 7, 8 => EtatIsolation::ISOLE,
        };
    }

    public function baies(): array
    {
        return [];
    }

    public function parois_opaques(): array
    {
        $parois = [];

        $parois[] = new XMLLncVirtuelParoiReader(
            surface: $this->surface_aue(),
            mitoyennete: Mitoyennete::EXTERIEUR,
            isolation: $this->isolation_paroi_aue(),
        );

        if ($this->surface() < $this->surface_aiu()) {
            $parois[] = new XMLLncVirtuelParoiReader(
                surface: $this->surface_aiu() - $this->surface(),
                mitoyennete: Mitoyennete::LOCAL_RESIDENTIEL,
                isolation: $this->isolation_paroi_aiu(),
            );
        }
        return $parois;
    }
}
