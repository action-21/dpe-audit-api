<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Audit\Audit as Entity;

/**
 * @property Consommation[] $consommations
 * @property Emission[] $emissions
 */
final class AuditData
{
    public function __construct(
        public ?float $volume_habitable,
        public ?float $surface_habitable,
        public ?float $hauteur_sous_plafond,
        public ?int $nombre_logements,
        public ?float $tbase,
        public ?bool $effet_joule,
        public ?float $cef,
        public ?float $cep,
        public ?float $eges,
        public ?Etiquette $etiquette_energie,
        public ?Etiquette $etiquette_climat,
        /** @var Consommation[] */
        public array $consommations,
        /** @var Emission[] */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            volume_habitable: $entity->data()->volume_habitable,
            surface_habitable: $entity->data()->surface_habitable,
            hauteur_sous_plafond: $entity->data()->hauteur_sous_plafond,
            nombre_logements: $entity->data()->nombre_logements,
            tbase: $entity->data()->tbase,
            effet_joule: $entity->data()->effet_joule,
            cef: $entity->data()->cef,
            cep: $entity->data()->cep,
            eges: $entity->data()->eges,
            etiquette_energie: $entity->data()->etiquette_energie,
            etiquette_climat: $entity->data()->etiquette_climat,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
