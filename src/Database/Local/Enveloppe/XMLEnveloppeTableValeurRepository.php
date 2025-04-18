<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Service\EnveloppeTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLEnveloppeTableValeurRepository implements EnveloppeTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function q4pa_conv(
        TypeBatiment $type_batiment,
        Annee $annee_construction,
        bool $presence_joints_menuiserie,
        EtatIsolation $isolation_murs_plafonds
    ): ?float {
        return $this->db->repository('enveloppe.q4pa_conv')
            ->createQuery()
            ->and('type_batiment', $type_batiment)
            ->and('presence_joints_menuiserie', $presence_joints_menuiserie)
            ->and('isolation_murs_plafonds', $isolation_murs_plafonds)
            ->andCompareTo('annee_construction', $annee_construction->value())
            ->getOne()
            ?->floatval('q4pa_conv');
    }
}
