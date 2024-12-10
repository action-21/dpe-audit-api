<?php

namespace App\Domain\Ventilation\Service;

use App\Domain\Ventilation\Data\{DebitRepository, PventRepository};
use App\Domain\Ventilation\Entity\Systeme;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};
use App\Domain\Ventilation\ValueObject\Performance;

final class MoteurPerformance
{
    public function __construct(
        private DebitRepository $debit_repository,
        private PventRepository $pvent_repository,
    ) {}

    public function calcule_performance_systeme(Systeme $entity): Performance
    {
        $debit = $this->debit(
            type_ventilation: $entity->type_ventilation(),
            type_generateur: $entity->generateur()?->signaletique()->type,
            type_vmc: $entity->generateur()?->signaletique()->type_vmc,
            presence_echangeur: $entity->generateur()?->signaletique()->presence_echangeur_thermique,
            systeme_collectif: $entity->generateur()?->generateur_collectif(),
            annee_installation: $entity->generateur()?->annee_installation() ?? $entity->ventilation()->annee_construction_batiment(),
        );
        $pvent = $this->pvent(
            type_ventilation: $entity->type_ventilation(),
            type_generateur: $entity->generateur()?->signaletique()->type,
            type_vmc: $entity->generateur()?->signaletique()->type_vmc,
            systeme_collectif: $entity->generateur()?->generateur_collectif(),
            annee_installation: $entity->generateur()?->annee_installation() ?? $entity->ventilation()->annee_construction_batiment(),
        );

        return Performance::create(
            qvarep_conv: $debit['qvarep_conv'],
            qvasouf_conv: $debit['qvasouf_conv'],
            smea_conv: $debit['smea_conv'],
            ratio_utilisation: $pvent['ratio_utilisation'],
            pvent_moy: $pvent['pvent_moy'],
            pvent: $pvent['pvent'],
        );
    }

    /**
     * @key float ratio_utilisation - Ratio du temps d'utilisation du mode mécanique
     * @key float pvent_moy - Puissance moyenne des auxiliaires en W
     * @key float pvent - Puissance des auxiliaires en W
     * 
     * @return array{ratio_utilisation: float, pvent_moy: float, pvent: float}
     */
    public function pvent(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?int $annee_installation,
        ?bool $systeme_collectif,
    ): array {
        if (null === $data = $this->pvent_repository->find_by(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            annee_installation: $annee_installation,
            systeme_collectif: $systeme_collectif,
        )) throw new \DomainException('Valeur forfaitaire Pvent non trouvée');

        return [
            'ratio_utilisation' => $data->ratio_utilisation,
            'pvent_moy' => $data->pvent_moy,
            'pvent' => $data->pvent,
        ];
    }

    /**
     * @key float qvarep_conv - Débit volumique conventionnel à reprendre (m3/(h.m²))
     * @key float qvasouf_conv - Débit volumique conventionnel à souffler (m3/(h.m²))
     * @key float smea_conv - Somme des modules d’entrée d’air sous 20 Pa par unité de surface habitable (m3/(h.m2))
     * 
     * @return array{qvarep_conv: float, qvasouf_conv: float, smea_conv: float}
     */
    public function debit(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur,
        ?bool $systeme_collectif,
        ?int $annee_installation,
    ): array {
        if (null === $data = $this->debit_repository->find_by(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur: $presence_echangeur,
            systeme_collectif: $systeme_collectif,
            annee_installation: $annee_installation,
        )) throw new \DomainException('Valeur forfaitaire Debit non trouvée');

        return [
            'qvarep_conv' => $data->qvarep_conv,
            'qvasouf_conv' => $data->qvasouf_conv,
            'smea_conv' => $data->smea_conv,
        ];
    }
}
