<?php

namespace App\Database\Local\PontThermique;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Enum;
use App\Domain\PontThermique\Data\{Kpt, KptRepository};
use App\Domain\PontThermique\Enum\{TypeIsolation, TypeLiaison, TypePose};

final class XMLKptRepository implements KptRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'pont_thermique.kpt';
    }

    public function find_by(
        TypeLiaison $type_liaison,
        ?TypeIsolation $type_isolation_mur,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
    ): ?Kpt {
        if ($largeur_dormant) {
            $valeurs = [50, 100];
            \usort($valeurs, fn(int $a, int $b): int => \abs(($a - $largeur_dormant)) - \abs(($b - $largeur_dormant)));
            $largeur_dormant = $valeurs[0];
        }
        $record = $this->createQuery()
            ->and('type_liaison', $type_liaison->id())
            ->and('type_isolation_mur', $type_isolation_mur?->id())
            ->and('type_isolation_plancher', $type_isolation_plancher?->id(), true)
            ->and('type_pose_ouverture', $type_pose_ouverture?->id(), true)
            ->and('presence_retour_isolation', $presence_retour_isolation, true)
            ->and('largeur_dormant', $largeur_dormant, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Kpt
    {
        return new Kpt(kpt: $record->get('kpt')->floatval(),);
    }
}
