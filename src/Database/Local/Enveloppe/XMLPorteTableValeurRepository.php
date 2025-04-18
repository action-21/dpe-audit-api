<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Porte\{Materiau, TypeVitrage};
use App\Domain\Enveloppe\Service\PorteTableValeurRepository;

final class XMLPorteTableValeurRepository extends XMLParoiTableValeurRepository implements PorteTableValeurRepository
{
    public function u(
        bool $presence_sas,
        EtatIsolation $isolation,
        Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?float {
        return $this->db->repository('porte.u')
            ->createQuery()
            ->and('presence_sas', $presence_sas)
            ->and('isolation', $isolation)
            ->and('materiau', $materiau)
            ->and('type_vitrage', $type_vitrage)
            ->andCompareTo('taux_vitrage', $taux_vitrage)
            ->getOne()
            ?->floatval('u');
    }
}
