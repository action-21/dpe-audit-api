<?php

namespace App\Application\Ecs\View;

use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Ecs\Engine\{GenerateurEngine, GenerateurEngineCollection};
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection};

class GenerateurView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $identifiant_reseau_chaleur,
        public readonly string $description,
        public readonly bool $position_volume_chauffe,
        public readonly Enum $type_generateur,
        public readonly Enum $usage,
        public readonly Enum $energie,
        public readonly Enum $type_stockage,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $position_volume_chauffe_stockage,
        public readonly ?float $volume_stockage,
        public readonly ?float $pn_saisi,
        public readonly ?float $rpn_saisi,
        public readonly ?float $qp0_saisi,
        public readonly ?float $pveilleuse_saisi,
        public readonly ?float $cop_saisi,
        public readonly ?int $annee_installation,
        public readonly ?float $cecs = null,
        public readonly ?float $cecs_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $cecs_j = null,
        /** @var ?array<float> */
        public readonly ?array $cecs_j_depensier = null,
        public readonly ?float $iecs = null,
        public readonly ?float $iecs_depensier = null,
        public readonly ?float $rgs = null,
        public readonly ?float $rgs_depensier = null,
        public readonly ?float $rg = null,
        public readonly ?float $rg_depensier = null,
        public readonly ?float $rd = null,
        public readonly ?float $rs = null,
        public readonly ?float $qgw = null,
        public readonly ?float $cop = null,
        public readonly ?float $cr = null,
        public readonly ?float $pecs = null,
        public readonly ?float $pn = null,
        public readonly ?float $rpn = null,
        public readonly ?float $qp0 = null,
        public readonly ?float $e = null,
        public readonly ?float $f = null,
        public readonly ?float $pveil = null,
        public readonly ?float $rdim = null,
    ) {
    }

    public static function from_entity(Generateur $entity): self
    {
        return new self(
            id: $entity->id(),
            identifiant_reseau_chaleur: $entity->identifiant_reseau_chaleur(),
            description: $entity->description(),
            position_volume_chauffe: $entity->position_volume_chauffe(),
            type_generateur: $entity->type_generateur(),
            usage: $entity->usage(),
            energie: $entity->energie(),
            type_stockage: $entity->stockage()->type_stockage,
            presence_ventouse: $entity->performance()->presence_ventouse,
            position_volume_chauffe_stockage: $entity->stockage()->position_volume_chauffe,
            volume_stockage: $entity->stockage()->volume_stockage?->valeur(),
            pn_saisi: $entity->performance()->pn?->valeur(),
            rpn_saisi: $entity->performance()->rpn?->valeur(),
            qp0_saisi: $entity->performance()->qp0?->valeur(),
            pveilleuse_saisi: $entity->performance()->pveilleuse?->valeur(),
            cop_saisi: $entity->performance()->cop?->valeur(),
            annee_installation: $entity->annee_installation(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(GenerateurCollection $collection): array
    {
        return \array_map(fn (Generateur $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(GenerateurEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            identifiant_reseau_chaleur: $entity->identifiant_reseau_chaleur(),
            description: $entity->description(),
            position_volume_chauffe: $entity->position_volume_chauffe(),
            type_generateur: $entity->type_generateur(),
            usage: $entity->usage(),
            energie: $entity->energie(),
            type_stockage: $entity->stockage()->type_stockage,
            presence_ventouse: $entity->performance()->presence_ventouse,
            position_volume_chauffe_stockage: $entity->stockage()->position_volume_chauffe,
            volume_stockage: $entity->stockage()->volume_stockage?->valeur(),
            pn_saisi: $entity->performance()->pn?->valeur(),
            rpn_saisi: $entity->performance()->rpn?->valeur(),
            qp0_saisi: $entity->performance()->qp0?->valeur(),
            pveilleuse_saisi: $entity->performance()->pveilleuse?->valeur(),
            cop_saisi: $entity->performance()->cop?->valeur(),
            annee_installation: $entity->annee_installation(),
            cecs: $engine->cecs(),
            cecs_depensier: $engine->cecs(scenario_depensier: true),
            cecs_j: \array_map(fn (Mois $mois): float => $engine->cecs_j($mois), Mois::cases()),
            cecs_j_depensier: \array_map(fn (Mois $mois): float => $engine->cecs_j(mois: $mois, scenario_depensier: true), Mois::cases()),
            iecs: $engine->iecs(),
            iecs_depensier: $engine->iecs(scenario_depensier: true),
            rgs: $engine->rgs(),
            rgs_depensier: $engine->rgs(scenario_depensier: true),
            rg: $engine->rg(),
            rg_depensier: $engine->rg(scenario_depensier: true),
            rd: $engine->rd(),
            rs: $engine->rs(),
            qgw: $engine->qgw(),
            cop: $engine->cop(),
            cr: $engine->cr(),
            pecs: $engine->pecs(),
            pn: $engine->pn(),
            rpn: $engine->rpn(),
            qp0: $engine->qp0(),
            e: $engine->e(),
            f: $engine->f(),
            pveil: $engine->pveil(),
            rdim: $engine->rdim(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(GenerateurEngineCollection $collection): array
    {
        return \array_map(fn (GenerateurEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
