<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Data\{CombustionRepository, PauxRepository, PnRepository, ScopRepository, Tfonc100Repository, Tfonc30Repository};
use App\Domain\Chauffage\Entity\{Emetteur, Generateur, Systeme};
use App\Domain\Chauffage\Enum\{CategorieGenerateur, EnergieGenerateur, TemperatureDistribution, TypeChaudiere, TypeEmission, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Performance;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Service\ExpressionResolver;
use App\Domain\Ecs\Service\MoteurDimensionnement as MoteurDimensionnementEcs;
use App\Domain\Simulation\Simulation;

final class MoteurPerformance
{
    public function __construct(
        private MoteurDimensionnementEcs $moteur_dimensionnement_ecs,
        private MoteurDimensionnement $moteur_dimensionnement,
        private PnRepository $pn_repository,
        private PauxRepository $paux_repository,
        private ScopRepository $scop_repository,
        private CombustionRepository $combustion_repository,
        private Tfonc30Repository $tfonc30_repository,
        private Tfonc100Repository $tfonc100_repository,
        private ExpressionResolver $expression_resolver,
    ) {}

    public function calcule_performance(Generateur $entity, Simulation $simulation): Performance
    {
        $pn = $this->calcule_pn($entity, $simulation);

        $combustion = $this->combustion_applicable(categorie_generateur: $entity->categorie()) ? $this->combustion(
            type_generateur: $entity->type_partie_chaudiere() ?? $entity->type(),
            energie_generateur: $entity->energie_partie_chaudiere() ?? $entity->energie(),
            annee_installation: $entity->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
            pn: $pn,
            presence_ventouse: $entity->signaletique()?->presence_ventouse,
            rpn_saisi: $entity->signaletique()?->rpn,
            rpint_saisi: $entity->signaletique()?->rpint,
            qp0_saisi: $entity->signaletique()?->qp0,
            pveilleuse_saisi: $entity->signaletique()?->pveilleuse,
        ) : [];

        $paux = $this->paux(
            pn: $pn,
            categorie_generateur: $entity->categorie(),
            presence_ventouse: $entity->signaletique()?->presence_ventouse,
        );

        return Performance::create(
            pn: $pn,
            paux: $paux,
            rpn: $combustion['rpn'] ?? null,
            rpint: $combustion['rpint'] ?? null,
            qp0: $combustion['qp0'] ?? null,
            pveilleuse: $combustion['pveilleuse'] ?? null,
            scop: $this->calcule_scop($entity),
            tfonc30: $this->calcule_tfonc30($entity),
            tfonc100: $this->calcule_tfonc100($entity),
        );
    }

    public function calcule_pn(Generateur $entity, Simulation $simulation): float
    {
        if ($entity->signaletique()?->pn)
            return $entity->signaletique()->pn;

        $pch = $this->moteur_dimensionnement->calcule_pch($entity, $simulation);
        $pecs = 0;

        if ($entity->generateur_mixte_id()) {
            if (null === $generateur_mixte = $simulation->ecs()->generateurs()->find_generateur_mixte(id: $entity->generateur_mixte_id()))
                throw new \InvalidArgumentException('Générateur mixte non trouvé');

            $pecs = $this->moteur_dimensionnement_ecs->calcule_pecs(
                entity: $generateur_mixte,
            );
        }

        $pdim = \max($pch, $pecs);

        return $entity->signaletique()?->type_chaudiere ? $this->pn_chaudiere(
            type_chaudiere: $entity->signaletique()->type_chaudiere,
            annee_installation: $entity->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
            pdim: $pdim,
        ) : $pdim;
    }

    public function calcule_scop(Generateur $entity): ?float
    {
        if (false === $this->scop_applicable(categorie_generateur: $entity->categorie()))
            return null;
        if (false === $entity->chauffage()->installations()->has_generateur($entity->id()))
            return null;

        $zone_climatique = $entity->chauffage()->audit()->zone_climatique();
        $annee_installation_generateur = $entity->annee_installation() ?? $entity->chauffage()->annee_construction_batiment();
        $scop_saisi = $entity->signaletique()?->scop;
        $scop = 0;

        /** @var Systeme */
        foreach ($entity->chauffage()->installations()->search_systemes_by_generateur($entity->id()) as $systeme) {
            if (false === $entity->id()->compare($systeme->generateur()->id()))
                continue;

            if (0 === $systeme->emetteurs()->count()) {
                $scop = \max($scop, $this->scop(
                    zone_climatique: $zone_climatique,
                    type_generateur: $entity->type(),
                    type_emission: TypeEmission::from_type_generateur($entity->type()),
                    annee_installation_generateur: $annee_installation_generateur,
                    scop_saisi: $scop_saisi,
                ));
            }
            /** @var Emetteur */
            foreach ($systeme->emetteurs() as $emetteur) {
                $scop = \max($scop, $this->scop(
                    zone_climatique: $zone_climatique,
                    type_generateur: $entity->type(),
                    type_emission: $emetteur->type_emission(),
                    annee_installation_generateur: $annee_installation_generateur,
                    scop_saisi: $scop_saisi,
                ));
            }
        }
        return $scop;
    }

    public function calcule_tfonc30(Generateur $entity): ?float
    {
        if (false === $this->tfonc30_applicable(categorie_generateur: $entity->categorie()))
            return null;
        if (false === $entity->chauffage()->installations()->has_generateur($entity->id()))
            return null;

        $tfonc30 = 0;
        /** @var Systeme */
        foreach ($entity->chauffage()->installations()->search_systemes_by_generateur($entity->id()) as $systeme) {
            /** @var Emetteur */
            foreach ($systeme->emetteurs() as $emetteur) {
                $tfonc30 = \max($tfonc30, $this->tfonc30(
                    categorie_generateur: $entity->categorie(),
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_generateur: $entity->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
                    annee_installation_emetteur: $emetteur->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
                    tfonc30_saisi: $entity->signaletique()?->tfonc30,
                ));
            }
        }
        return $tfonc30;
    }

    public function calcule_tfonc100(Generateur $entity): ?float
    {
        if (false === $this->tfonc100_applicable(categorie_generateur: $entity->categorie()))
            return null;
        if (false === $entity->chauffage()->installations()->has_generateur($entity->id()))
            return null;

        $tfonc100 = 0;
        /** @var Systeme */
        foreach ($entity->chauffage()->installations()->search_systemes_by_generateur($entity->id()) as $systeme) {
            /** @var Emetteur */
            foreach ($systeme->emetteurs() as $emetteur) {
                $tfonc100 = \max($tfonc100, $this->tfonc100(
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_emetteur: $emetteur->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
                    tfonc100_saisi: $entity->signaletique()?->tfonc100,
                ));
            }
        }
        return $tfonc100;
    }

    /**
     * Puissance nominale du radiateur en kW
     * 
     * @param float $pch - Puissance de chauffage en kW
     * @param float $n - Nombre de radiateurs
     */
    public function pn_radiateur(float $pch, float $n): float
    {
        return $pch / $n;
    }

    /**
     * Puissance nominale de la chaudière en kW
     * 
     * @param float $pdim - Puissance de dimensionnement en kW
     */
    public function pn_chaudiere(
        TypeChaudiere $type_chaudiere,
        int $annee_installation,
        float $pdim,
    ): float {
        if (null === $data = $this->pn_repository->find_by(
            type_chaudiere: $type_chaudiere,
            annee_installation: $annee_installation,
            pdim: $pdim,
        )) throw new \DomainException('Valeur forfaitaire Pn non trouvée');

        return $this->expression_resolver->evalue(
            expression: $data->pn,
            variables: ['Pdim' => $pdim],
        );
    }

    /**
     * Puissance de l'auxiliaire de génération en W
     * 
     * @param float $pn - Puissance nominale du générateur en kW
     */
    public function paux(CategorieGenerateur $categorie_generateur, float $pn, ?bool $presence_ventouse): float
    {
        if (null === $data = $this->paux_repository->find_by(
            categorie_generateur: $categorie_generateur,
            presence_ventouse: $presence_ventouse,
        )) throw new \DomainException("Valeur forfaitaire Paux non trouvée");

        $pn = $data->pn_max ? \min($pn, $data->pn_max) : $pn;
        return $data->g + $data->h * $pn;
    }

    /**
     * SCOP forfaitaire du générateur
     * 
     * @param int $annee_installation_generateur - Année d'installation du générateur ou à défaut année de construction du bâtiment
     * @param float|null $scop_saisi - SCOP saisi
     */
    public function scop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        int $annee_installation_generateur,
        ?float $scop_saisi,
    ): float {
        if ($scop_saisi)
            return $scop_saisi;

        if (null === $data = $this->scop_repository->find_by(
            zone_climatique: $zone_climatique,
            type_generateur: $type_generateur,
            type_emission: $type_emission,
            annee_installation_generateur: $annee_installation_generateur,
        )) throw new \DomainException("Valeur forfaitaire SCOP non trouvée");

        return $data->scop ?? $data->cop;
    }

    public function scop_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::PAC,
            CategorieGenerateur::PAC_HYBRIDE,
        ]);
    }

    /**
     * Performance fofaitaire de combustion
     * 
     * @param int $annee_installation - Année d'installation du générateur ou à défaut année de construction du bâtiment
     * @param float $pn - Puissance nominale du générateur (kW)
     * @param null|float $rpn_saisi - Rendement à pleine charge saisi
     * @param null|float $rpint_saisi - Rendement à charge intermédiaire saisi
     * @param null|float $qp0_saisi - Pertes à l'arrêt du générateur saisi
     * @param null|float $pveilleuse_saisi - Puissance de la veilleuse saisi
     * 
     * @return array{rpn: float, rpint: float|null, qp0: float|null, pveilleuse: float|null}
     */
    public function combustion(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        int $annee_installation,
        float $pn,
        ?bool $presence_ventouse,
        ?float $rpn_saisi,
        ?float $rpint_saisi,
        ?float $qp0_saisi,
        ?float $pveilleuse_saisi,
    ): array {
        if (null === $data = $this->combustion_repository->find_by(
            type_generateur: $type_generateur,
            energie_generateur: $energie_generateur,
            annee_installation_generateur: $annee_installation,
            pn: $pn,
        )) throw new \DomainException("Valeurs forfaitaires de combustion non trouvées");

        $e = $presence_ventouse ? 1.75 : 2.5;
        $f = $presence_ventouse ? -0.55 : -0.8;
        $pn = $data->pn_max ? \min($pn, $data->pn_max) : $pn;
        $variables = ['E' => $e, 'F' => $f, 'Pn' => $pn];

        $rpn = $rpn_saisi ?? $this->expression_resolver->evalue(expression: $data->rpn, variables: $variables);
        $rpint = $rpint_saisi ?? ($data->rpint ? $this->expression_resolver->evalue(expression: $data->rpint, variables: $variables) : null);
        $qp0 = $qp0_saisi ?? ($data->qp0 ? $this->expression_resolver->evalue(expression: $data->qp0, variables: $variables) : null);
        $pveilleuse = $pveilleuse_saisi ?? ($data->pveilleuse ? $this->expression_resolver->evalue(expression: $data->pveilleuse, variables: $variables) : 0);

        return ['rpn' => $rpn, 'rpint' => $rpint, 'qp0' => $qp0, 'pveilleuse' => $pveilleuse];
    }

    public function combustion_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_BOIS,
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
            CategorieGenerateur::POELE_BOIS_BOUILLEUR,
            CategorieGenerateur::GENERATEUR_AIR_CHAUD,
            CategorieGenerateur::RADIATEUR_GAZ,
            CategorieGenerateur::PAC_HYBRIDE,
        ]);
    }

    /**
     * Température de fonctionnement à 30% de charge
     * 
     * @param int $annee_installation_generateur - Année d'installation du générateur ou à défaut année de construction du bâtiment
     * @param int $annee_installation_emetteur - Année d'installation de l'émetteur ou à défaut année de construction du bâtiment
     * @param null|float $tfonc30_saisi - Température de fonctionnement à 30% de charge saisi
     */
    public function tfonc30(
        CategorieGenerateur $categorie_generateur,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_generateur,
        int $annee_installation_emetteur,
        ?float $tfonc30_saisi,
    ): float {
        if ($tfonc30_saisi)
            return $tfonc30_saisi;
        if (null === $data = $this->tfonc30_repository->find_by(
            categorie_generateur: $categorie_generateur,
            temperature_distribution: $temperature_distribution,
            annee_installation_generateur: $annee_installation_generateur,
            annee_installation_emetteur: $annee_installation_emetteur,
        )) throw new \DomainException("Valeur forfaitaire Tfonc30 non trouvée.");

        return $data->tfonc30;
    }

    public function tfonc30_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
            CategorieGenerateur::PAC_HYBRIDE,
        ]);
    }

    /**
     * Température de fonctionnement à 100% de charge
     * 
     * @param int $annee_installation_emetteur - Année d'installation de l'émetteur ou à défaut année de construction du bâtiment
     * @param null|float $tfonc100_saisi - Température de fonctionnement à 100% de charge saisi
     */
    public function tfonc100(
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
        ?float $tfonc100_saisi,
    ): float {
        if ($tfonc100_saisi)
            return $tfonc100_saisi;
        if (null === $data = $this->tfonc100_repository->find_by(
            temperature_distribution: $temperature_distribution,
            annee_installation_emetteur: $annee_installation_emetteur,
        )) throw new \DomainException("Valeur forfaitaire Tfonc100 non trouvée.");

        return $data->tfonc100;
    }

    public function tfonc100_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
            CategorieGenerateur::PAC_HYBRIDE,
        ]);
    }
}
