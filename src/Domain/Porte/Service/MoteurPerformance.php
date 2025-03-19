<?php

namespace App\Domain\Porte\Service;

use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Porte\Data\{BporteRepository, UporteRepository};
use App\Domain\Porte\Enum\{EtatIsolation, EtatPerformance, Materiau, Mitoyennete, TypeVitrage};
use App\Domain\Porte\Porte;
use App\Domain\Porte\ValueObject\Performance;

/**
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 */
final class MoteurPerformance extends MoteurCalcul
{
    public function __construct(
        private UporteRepository $u_repository,
        private BporteRepository $b_repository,
    ) {}

    public static function materiau_defaut(): Materiau
    {
        return Materiau::PVC;
    }

    public static function isolation_defaut(): EtatIsolation
    {
        return EtatIsolation::NON_ISOLE;
    }

    public static function type_vitrage_defaut(): TypeVitrage
    {
        return TypeVitrage::SIMPLE_VITRAGE;
    }

    /**
     * Surface déperditive en m²
     */
    public function sdep(float $surface, Mitoyennete $mitoyennete): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $surface;
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(Mitoyennete $mitoyennete, ?float $b_lnc,): float
    {
        if (null !== $b_lnc) {
            return $b_lnc;
        }
        if (null === $data = $this->b_repository->find_by(mitoyennete: $mitoyennete)) {
            throw new \DomainException('Valeur forfaitaire b non trouvée');
        }
        return $data->b;
    }

    /**
     * Coefficient de transmission thermique en W/m².K
     * 
     * @param int|float $taux_vitrage - Taux de vitrage en %
     * @param null|float $u_saisi - Coefficient de transmission thermique connu et justifié en W/m².K
     */
    public function uporte(
        bool $presence_sas,
        ?EtatIsolation $etat_isolation,
        ?Materiau $materiau,
        ?float $taux_vitrage,
        ?TypeVitrage $type_vitrage,
        ?float $u_saisi,
    ): float {
        if ($u_saisi) {
            return $u_saisi;
        }

        $this->valeurs_forfaitaires()->add('uporte');

        if (null === $materiau) {
            $this->valeurs_forfaitaires()->add('materiau');
            $materiau = self::materiau_defaut();
        }
        if (null === $etat_isolation) {
            $this->valeurs_forfaitaires()->add('etat_isolation');
            $etat_isolation = self::isolation_defaut();
        }
        if ($taux_vitrage > 0 && null === $type_vitrage) {
            $this->valeurs_forfaitaires()->add('type_vitrage');
            $type_vitrage = self::type_vitrage_defaut();
        }

        if (null === $data = $this->u_repository->find_by(
            presence_sas: $presence_sas,
            isolation: $etat_isolation,
            materiau: $materiau,
            taux_vitrage: $taux_vitrage,
            type_vitrage: $type_vitrage
        )) throw new \DomainException('Valeur forfaitaire Uporte non trouvée');

        return $data->u;
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
     * Etat de performance de la porte
     * 
     * @see Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine
     * 
     * @float $uporte - Coefficient de transmission thermique en W/m².K
     */
    public function etat_peformance(float $uporte): EtatPerformance
    {
        return match (true) {
            $uporte >= 3 => EtatPerformance::INSUFFISANTE,
            $uporte >= 2.2 => EtatPerformance::MOYENNE,
            $uporte >= 1.6 => EtatPerformance::BONNE,
            $uporte < 1.6 => EtatPerformance::TRES_BONNE,
        };
    }

    public function __invoke(Porte $entity): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL) {
            return null;
        }

        $this->valeurs_forfaitaires()->reset();

        $sdep = $this->sdep(
            surface: $entity->position()->surface,
            mitoyennete: $entity->position()->mitoyennete,
        );
        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: false)
        );
        $u = $this->uporte(
            presence_sas: $entity->presence_sas(),
            etat_isolation: $entity->isolation(),
            materiau: $entity->materiau(),
            taux_vitrage: $entity->vitrage()->taux_vitrage,
            type_vitrage: $entity->vitrage()->type_vitrage,
            u_saisi: $entity->u(),
        );
        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            sdep: $sdep,
            b: $b,
            u: $u,
        );

        return Performance::create(
            sdep: $sdep,
            b: $b,
            u: $u,
            dp: $dp,
            etat_performance: $this->etat_peformance($u),
            valeurs_forfaitaires: $this->valeurs_forfaitaires(),
        );
    }
}
