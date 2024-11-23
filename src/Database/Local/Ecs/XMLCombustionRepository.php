<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Data\{Combustion, CombustionRepository};
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

final class XMLCombustionRepository implements CombustionRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.combustion';
    }

    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?Combustion {
        $record = $this->createQuery()
            ->and('type_generateur', $type_generateur->value)
            ->and('energie_generateur', $energie_generateur->value, true)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->andCompareTo('pn', $pn)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Combustion
    {
        return new Combustion(
            pn_max: $element->get('pn_max')->floatval(),
            rpn: $element->get('rpn')->strval(),
            qp0: $element->get('qp0')->strval(),
            pveilleuse: $element->get('pveilleuse')->floatval(),
        );
    }
}
