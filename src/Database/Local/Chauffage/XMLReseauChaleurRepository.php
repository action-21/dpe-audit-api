<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Common\ValueObject\{Id, Pourcentage};
use App\Domain\Chauffage\Entity\ReseauChaleur;
use App\Domain\Chauffage\Repository\ReseauChaleurRepository;

final class XMLReseauChaleurRepository implements ReseauChaleurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function find(Id $id): ?ReseauChaleur
    {
        $record = $this->db->repository('reseau_chaleur')
            ->createQuery()
            ->and('id', $id)
            ->getOne();

        return $record ? new ReseauChaleur(
            id: $id,
            contenu_co2: Pourcentage::from($record->floatval('contenu_co2')),
            contenu_co2_acv: Pourcentage::from($record->floatval('contenu_co2_acv')),
            taux_enr: Pourcentage::from($record->floatval('taux_enr')),
        ) : null;
    }
}
