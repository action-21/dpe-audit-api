<?php

namespace App\Domain\PontThermique\Service;

use App\Domain\PontThermique\Data\KptRepository;
use App\Domain\PontThermique\Enum\{TypeIsolation, TypeLiaison, TypePose};
use App\Domain\PontThermique\PontThermique;
use App\Domain\PontThermique\ValueObject\Performance;

final class MoteurPerformance
{
    public function __construct(private KptRepository $kpt_repository,) {}

    public function calcule_performance(PontThermique $entity): Performance
    {
        $kpt = $this->kpt(
            type_liaison: $entity->liaison()->type,
            type_isolation_mur: $entity->type_isolation_mur(),
            type_isolation_plancher: $entity->type_isolation_plancher_bas() ?? $entity->type_isolation_plancher_haut(),
            type_pose_ouverture: $entity->type_pose_ouverture(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            largeur_dormant: $entity->largeur_dormant(),
            kpt_saisi: $entity->kpt(),
        );
        $pt = $this->pt(
            longueur: $entity->longueur(),
            pont_thermique_partiel: $entity->liaison()->pont_thermique_partiel,
            kpt: $kpt,
        );

        return Performance::create(kpt: $kpt, pt: $pt);
    }

    /**
     * Déperdition thermique en W/K
     * 
     * @param float $longueur - Longueur du pont thermique en m
     * @param bool $pont_thermique_partiel - Pont thermique partiel dans le cas d'une liaison Mur/Refend ou Mur/Plancher intermédiaire
     * @param float $kpt - Coefficient de transmission thermique en W/m.K
     */
    public function pt(float $longueur, bool $pont_thermique_partiel, float $kpt): float
    {
        return $kpt * $longueur * ($pont_thermique_partiel ? 0.5 : 1);
    }

    /**
     * Coefficient de transmission thermique en W/m.K
     * 
     * @param null|int $largeur_dormant - Largeur du dormant en mm
     * @param null|float $kpt_saisi - Coefficient de transmission thermique connu et justifié en W/m.K
     */
    public function kpt(
        TypeLiaison $type_liaison,
        ?TypeIsolation $type_isolation_mur,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
        ?float $kpt_saisi,
    ): float {
        if ($kpt_saisi)
            return $kpt_saisi;

        if (null === $data = $this->kpt_repository->find_by(
            type_liaison: $type_liaison,
            type_isolation_mur: $type_isolation_mur,
            type_isolation_plancher: $type_isolation_plancher,
            type_pose_ouverture: $type_pose_ouverture,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
        )) throw new \DomainException('Valeur forfaitaire Kpt non trouvée');

        return $data->kpt;
    }
}
