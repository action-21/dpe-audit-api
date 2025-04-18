<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;
use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeIsolation, TypePose};
use App\Domain\Enveloppe\Service\PontThermiqueTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLPontThermiqueTableValeurRepository implements PontThermiqueTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function kpt(
        TypeLiaison $type_liaison,
        EtatIsolation $etat_isolation_mur,
        ?TypeIsolation $type_isolation_mur,
        ?EtatIsolation $etat_isolation_plancher,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose,
        ?bool $presence_retour_isolation,
        ?float $largeur_dormant
    ): ?float {
        if ($largeur_dormant) {
            $valeurs = [50, 100];
            \usort($valeurs, fn(int $a, int $b): int => \abs(($a - $largeur_dormant)) - \abs(($b - $largeur_dormant)));
            $largeur_dormant = $valeurs[0];
        }
        return $this->db->repository('pont_thermique.kpt')
            ->createQuery()
            ->and('type_liaison', $type_liaison)
            ->and('etat_isolation_mur', $etat_isolation_mur)
            ->and('type_isolation_mur', $type_isolation_mur)
            ->and('etat_isolation_plancher', $etat_isolation_plancher)
            ->and('type_isolation_plancher', $type_isolation_plancher)
            ->and('type_pose_ouverture', $type_pose)
            ->and('presence_retour_isolation', $presence_retour_isolation)
            ->and('largeur_dormant', $largeur_dormant)
            ->getOne()
            ?->floatval('kpt');
    }
}
