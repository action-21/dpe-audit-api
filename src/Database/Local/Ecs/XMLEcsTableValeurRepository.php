<?php

namespace App\Database\Local\Ecs;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\{Annee, Pourcentage};
use App\Domain\Ecs\Enum\{BouclageReseau, EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeGenerateur, TypeChaudiere, UsageEcs};
use App\Domain\Ecs\Service\EcsTableValeurRepository;
use App\Services\ExpressionResolver\ExpressionResolver;
use App\Database\Local\{XMLTableElement, XMLTableDatabase};

final class XMLEcsTableValeurRepository implements EcsTableValeurRepository
{
    public function __construct(
        private readonly XMLTableDatabase $db,
        private ExpressionResolver $expression_resolver,
    ) {}

    public function pn(TypeChaudiere $type_chaudiere, Annee $annee_installation_generateur, float $pdim): ?float
    {
        return $this->db->repository('ecs.pn')
            ->createQuery()
            ->and('type_chaudiere', $type_chaudiere)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur->value())
            ->andCompareTo('pdim', $pdim)
            ->getOne()
            ?->to(fn(XMLTableElement $record) => $this->expression_resolver->evalue($record->strval('pn'), ['Pdim' => $pdim]));
    }

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $presence_ventouse,
        float $pn
    ): ?float {
        return $this->db->repository('ecs.paux')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('presence_ventouse', $presence_ventouse)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ?? $pn;
                $expression = $record->strval('paux');
                return $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
            });
    }

    public function rd(
        bool $production_volume_habitable,
        bool $reseau_collectif,
        bool $alimentation_contigue,
        ?BouclageReseau $bouclage_reseau
    ): ?float {
        return $this->db->repository('ecs.rd')
            ->createQuery()
            ->and('production_volume_habitable', $production_volume_habitable)
            ->and('reseau_collectif', $reseau_collectif)
            ->and('alimentation_contigue', $alimentation_contigue)
            ->and('bouclage_reseau', $bouclage_reseau)
            ->getOne()
            ?->floatval('rd');
    }

    public function rg(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur): ?float
    {
        return $this->db->repository('ecs.rg')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->getOne()
            ?->floatval('rg');
    }

    public function cr(
        TypeGenerateur $type_generateur,
        float $volume_stockage,
        ?LabelGenerateur $label_generateur
    ): ?float {
        return $this->db->repository('ecs.cr')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('label_generateur', $label_generateur)
            ->andCompareTo('volume_stockage', $volume_stockage)
            ->getOne()
            ?->floatval('cr');
    }

    public function cop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        Annee $annee_installation
    ): ?float {
        return $this->db->repository('ecs.cop')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('type_generateur', $type_generateur)
            ->andCompareTo('annee_installation', $annee_installation->value())
            ->getOne()
            ?->floatval('cop');
    }

    public function fecs(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        UsageEcs $usage_solaire,
        Annee $annee_installation,
    ): ?Pourcentage {
        return $this->db->repository('ecs.fecs')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('type_batiment', $type_batiment)
            ->and('usage_solaire', $usage_solaire)
            ->andCompareTo('anciennete_installation', (int) date('Y') - $annee_installation->value())
            ->getOne()
            ?->to(fn(XMLTableElement $record) => Pourcentage::from_decimal($record->floatval('fecs')));
    }

    public function rpn(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation,
        float $pn
    ): ?Pourcentage {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation', $annee_installation->value())
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('rpn');
                $value = $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
                return Pourcentage::from_decimal($value);
            });
    }

    public function qp0(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation,
        float $pn,
        float $e,
        float $f
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation', $annee_installation->value())
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn, $e, $f) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('qp0');
                $variables = ['Pn' => $pn, 'E' => $e, 'F' => $f];
                return $this->expression_resolver->evalue($expression, $variables);
            });
    }

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation', $annee_installation->value())
            ->getOne()
            ?->floatval('pveilleuse');
    }
}
