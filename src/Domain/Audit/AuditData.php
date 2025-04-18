<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\ValueObject\{Consommations, Emissions, SollicitationsExterieures};
use Webmozart\Assert\Assert;

final class AuditData
{
    public function __construct(
        public readonly ?float $volume_habitable,
        public readonly ?float $surface_habitable,
        public readonly ?float $hauteur_sous_plafond,
        public readonly ?int $nombre_logements,
        public readonly ?SollicitationsExterieures $sollicitations_exterieures,
        public readonly ?float $tbase,
        public readonly ?bool $effet_joule,
        public readonly ?Emissions $emissions,
        public readonly ?Consommations $consommations,
        public readonly ?Etiquette $etiquette_energie,
        public readonly ?Etiquette $etiquette_climat,
    ) {}

    public static function create(
        ?float $volume_habitable = null,
        ?float $surface_habitable = null,
        ?float $hauteur_sous_plafond = null,
        ?int $nombre_logements = null,
        ?SollicitationsExterieures $sollicitations_exterieures = null,
        ?float $tbase = null,
        ?bool $effet_joule = null,
        ?Emissions $emissions = null,
        ?Consommations $consommations = null,
        ?Etiquette $etiquette_energie = null,
        ?Etiquette $etiquette_climat = null,
    ): self {
        Assert::nullOrGreaterThan($volume_habitable, 0);
        Assert::nullOrGreaterThan($surface_habitable, 0);
        Assert::nullOrGreaterThan($hauteur_sous_plafond, 0);
        Assert::nullOrGreaterThan($nombre_logements, 0);

        return new self(
            volume_habitable: $volume_habitable,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            nombre_logements: $nombre_logements,
            sollicitations_exterieures: $sollicitations_exterieures,
            tbase: $tbase,
            effet_joule: $effet_joule,
            emissions: $emissions,
            consommations: $consommations,
            etiquette_energie: $etiquette_energie,
            etiquette_climat: $etiquette_climat,
        );
    }

    public function with(
        ?float $volume_habitable = null,
        ?float $surface_habitable = null,
        ?float $hauteur_sous_plafond = null,
        ?int $nombre_logements = null,
        ?SollicitationsExterieures $sollicitations_exterieures = null,
        ?float $tbase = null,
        ?bool $effet_joule = null,
        ?Emissions $emissions = null,
        ?Consommations $consommations = null,
        ?Etiquette $etiquette_energie = null,
        ?Etiquette $etiquette_climat = null,
    ): self {
        return self::create(
            volume_habitable: $volume_habitable ?? $this->volume_habitable,
            surface_habitable: $surface_habitable ?? $this->surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond ?? $this->hauteur_sous_plafond,
            nombre_logements: $nombre_logements ?? $this->nombre_logements,
            sollicitations_exterieures: $sollicitations_exterieures ?? $this->sollicitations_exterieures,
            tbase: $tbase ?? $this->tbase,
            effet_joule: $effet_joule ?? $this->effet_joule,
            emissions: $emissions ?? $this->emissions,
            consommations: $consommations ?? $this->consommations,
            etiquette_energie: $etiquette_energie ?? $this->etiquette_energie,
            etiquette_climat: $etiquette_climat ?? $this->etiquette_climat,
        );
    }
}
