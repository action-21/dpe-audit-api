<?php

namespace App\Domain\Porte\Service;

use App\Domain\Common\Error\DomainError;
use App\Domain\Porte\Data\{BporteRepository, UporteRepository};
use App\Domain\Porte\Enum\{EtatIsolation, Mitoyennete, NatureMenuiserie, TypeVitrage};
use App\Domain\Porte\Porte;
use App\Domain\Porte\ValueObject\Performance;

/**
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 */
final class MoteurPerformance
{
    public function __construct(
        private UporteRepository $u_repository,
        private BporteRepository $b_repository,
    ) {}

    public function calcule_performance(Porte $entity): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return null;

        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: false)
        );
        $u = $this->uporte(
            presence_sas: $entity->caracteristique()->presence_sas,
            etat_isolation: $entity->caracteristique()->isolation,
            nature_menuiserie: $entity->caracteristique()->nature_menuiserie,
            taux_vitrage: $entity->caracteristique()->taux_vitrage,
            type_vitrage: $entity->caracteristique()->type_vitrage,
            u_saisi: $entity->caracteristique()->u,
        );
        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            sdep: $entity->surface_deperditive(),
            b: $b,
            u: $u,
        );

        return Performance::create(b: $b, u: $u, dp: $dp);
    }

    /**
     * Déperditions thermiques en W/K
     * 
     * @param float $sdep - Surface déperditive en m²
     * @param float $b - Coefficient de réduction des déperditions thermiques
     * @param float $u - Coefficient de transmission thermique en W/m².K
     */
    public function dp(Mitoyennete $mitoyennete, float $sdep, float $b, float $u): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $sdep * $b * $u;
    }

    /**
     * Coefficient de transmission thermique en W/m².K
     * 
     * @param int|float $taux_vitrage - Taux de vitrage en %
     * @param null|float $u_saisi - Coefficient de transmission thermique connu et justifié en W/m².K
     */
    public function uporte(
        bool $presence_sas,
        EtatIsolation $etat_isolation,
        NatureMenuiserie $nature_menuiserie,
        int|float $taux_vitrage,
        ?TypeVitrage $type_vitrage,
        ?float $u_saisi,
    ): float {
        if ($u_saisi)
            return $u_saisi;

        if (null === $data = $this->u_repository->find_by(
            presence_sas: $presence_sas,
            isolation: $etat_isolation,
            nature_menuiserie: $nature_menuiserie,
            taux_vitrage: $taux_vitrage,
            type_vitrage: $type_vitrage
        )) throw new \DomainException('Valeur forfaitaire Uporte non trouvée');

        return $data->u;
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(Mitoyennete $mitoyennete, ?float $b_lnc,): float
    {
        if (null !== $b_lnc)
            return $b_lnc;

        if (null === $data = $this->b_repository->find_by(mitoyennete: $mitoyennete)) {
            throw new \DomainException('Valeur forfaitaire b non trouvée');
        }

        return $data->b;
    }
}
