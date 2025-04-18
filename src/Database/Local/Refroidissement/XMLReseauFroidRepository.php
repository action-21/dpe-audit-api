<?php

namespace App\Database\Local\Refroidissement;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Common\ValueObject\{Id, Pourcentage};
use App\Domain\Refroidissement\Entity\ReseauFroid;
use App\Domain\Refroidissement\Repository\ReseauFroidRepository;

final class XMLReseauFroidRepository implements ReseauFroidRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function find(Id $id): ?ReseauFroid
    {
        $record = $this->db->repository('reseau_froid')
            ->createQuery()
            ->and('id', $id)
            ->getOne();

        return $record ? new ReseauFroid(
            id: $id,
            contenu_co2: Pourcentage::from($record->floatval('contenu_co2')),
            contenu_co2_acv: Pourcentage::from($record->floatval('contenu_co2_acv')),
            taux_enr: Pourcentage::from($record->floatval('taux_enr')),
        ) : null;
    }
}
