<?php

namespace App\Application\Ventilation\View;

use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Common\Table\TableValue;
use App\Domain\Ventilation\Engine\{VentilationEngine, VentilationEngineCollection};
use App\Domain\Ventilation\Entity\{Ventilation, VentilationCollection};

class VentilationView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly Enum $type_ventilation,
        public readonly ?Enum $type_installation,
        public readonly ?int $annee_installation,
        /** @var ?array<float> */
        public readonly null|array $caux_j = null,
        public readonly null|float $caux = null,
        public readonly null|float $qvarep_conv = null,
        public readonly null|float $qvasouf_conv = null,
        public readonly null|float $smea_conv = null,
        public readonly null|float $pvent_moy = null,
        public readonly null|float $pvent = null,
        public readonly ?float $ratio_temps_utilisation = null,
        public readonly ?float $rdim = null,
        public readonly ?TableValue $table_debit = null,
        public readonly ?TableValue $table_pvent = null,
    ) {
    }

    public static function from_entity(Ventilation $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            type_ventilation: $entity->type_ventilation(),
            type_installation: $entity->type_installation(),
            annee_installation: $entity->annee_installation()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(VentilationCollection $collection): array
    {
        return \array_map(fn (Ventilation $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(VentilationEngine $engine): self
    {
        $entity = $engine->input();

        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            type_ventilation: $entity->type_ventilation(),
            type_installation: $entity->type_installation(),
            annee_installation: $entity->annee_installation()?->valeur(),
            caux: $engine->caux(),
            qvarep_conv: $engine->qvarep_conv(),
            qvasouf_conv: $engine->qvasouf_conv(),
            smea_conv: $engine->smea_conv(),
            pvent_moy: $engine->pvent_moy(),
            pvent: $engine->pvent(),
            ratio_temps_utilisation: $engine->ratio_temps_utilisation(),
            rdim: $engine->rdim(),
            table_debit: $engine->table_debit(),
            table_pvent: $engine->table_pvent(),
            caux_j: \array_map(fn (Mois $mois): float|false => $engine->caux_j($mois), Mois::cases()),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(VentilationEngineCollection $collection): array
    {
        return \array_map(fn (VentilationEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
