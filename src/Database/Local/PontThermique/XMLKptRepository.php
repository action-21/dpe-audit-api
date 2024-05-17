<?php

namespace App\Database\Local\PontThermique;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Enum;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\Table\{Kpt, KptRepository};

final class XMLKptRepository implements KptRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'pont_thermique.kpt.xml';
    }

    public function find(int $id): ?Kpt
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeLiaison $type_liaison,
        ?Enum $type_isolation_mur,
        ?Enum $type_isolation_plancher,
        ?Enum $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
    ): ?Kpt {
        if ($largeur_dormant) {
            $valeurs = [5, 10];
            \usort($valeurs, fn (int $a, int $b): int => \abs(($a - $largeur_dormant / 10)) - \abs(($b - $largeur_dormant / 10)));
            $largeur_dormant = $valeurs[0];
        }
        $record = $this->createQuery()
            ->and(\sprintf('type_liaison_id = "%s"', $type_liaison->id()))
            ->and(\sprintf('type_isolation_mur_id = "%s" or type_isolation_mur_id = ""', $type_isolation_mur?->id()))
            ->and(\sprintf('type_isolation_plancher_id = "%s" or type_isolation_plancher_id = ""', $type_isolation_plancher?->id()))
            ->and(\sprintf('type_pose_ouverture_id = "%s" or type_pose_ouverture_id = ""', $type_pose_ouverture?->id()))
            ->and(\sprintf('presence_retour_isolation = "%s" or presence_retour_isolation = ""', null !== $presence_retour_isolation ? (int) $presence_retour_isolation : null))
            ->and(\sprintf('largeur_dormant = "%s" or largeur_dormant = ""', $largeur_dormant))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Kpt
    {
        return new Kpt(
            id: $record->id(),
            kpt: (float) $record->k,
        );
    }
}
