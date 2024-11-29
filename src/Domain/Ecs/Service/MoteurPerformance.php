<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Chauffage\Service\MoteurDimensionnement as MoteurDimensionnementChauffage;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Service\ExpressionResolver;
use App\Domain\Ecs\Data\{CombustionRepository, CopRepository, PauxRepository, PnRepository};
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\{CategorieGenerateur, EnergieGenerateur, TypeChaudiere, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Performance;
use App\Domain\Simulation\Simulation;

final class MoteurPerformance
{
    public function __construct(
        private MoteurDimensionnementChauffage $moteur_dimensionnement_chauffage,
        private MoteurDimensionnement $moteur_dimensionnement,
        private CopRepository $cop_repository,
        private PnRepository $pn_repository,
        private PauxRepository $paux_repository,
        private CombustionRepository $combustion_repository,
        private ExpressionResolver $expression_resolver,
    ) {}

    public function calcule_performance(Generateur $entity, Simulation $simulation): Performance
    {
        $pn = $this->calcule_pn($entity, $simulation);
        $paux = $this->paux(categorie_generateur: $entity->categorie(), pn: $pn, presence_ventouse: $entity->signaletique()?->presence_ventouse);

        $cop = $this->cop_applicable(type_generateur: $entity->type()) ? $this->cop(
            zone_climatique: $entity->ecs()->audit()->zone_climatique(),
            type_generateur: $entity->type(),
            annee_installation: $entity->annee_installation() ?? $entity->ecs()->audit()->annee_construction_batiment(),
            cop_saisi: $entity->signaletique()?->cop,
        ) : null;

        $combustion = $this->combustion_applicable(categorie_generateur: $entity->categorie()) ? $this->combustion(
            type_generateur: $entity->type(),
            energie_generateur: $entity->energie(),
            annee_installation: $entity->annee_installation() ?? $entity->ecs()->audit()->annee_construction_batiment(),
            pn: $pn,
            presence_ventouse: $entity->signaletique()?->presence_ventouse,
            rpn_saisi: $entity->signaletique()?->rpn,
            qp0_saisi: $entity->signaletique()?->qp0,
            pveilleuse_saisi: $entity->signaletique()?->pveilleuse,
        ) : [];

        return Performance::create(
            pn: $pn,
            paux: $paux,
            cop: $cop,
            rpn: $combustion['rpn'] ?? null,
            qp0: $combustion['qp0'] ?? null,
            pveilleuse: $combustion['pveilleuse'] ?? null,
        );
    }

    public function calcule_pn(Generateur $entity, Simulation $simulation): float
    {
        if ($entity->signaletique()?->pn)
            return $entity->signaletique()->pn;

        $pecs = $this->moteur_dimensionnement->calcule_pecs($entity);
        $pch = 0;

        if ($entity->generateur_mixte_id()) {
            if (null === $generateur_mixte = $simulation->chauffage()->generateurs()->find_generateur_mixte(id: $entity->generateur_mixte_id()))
                throw new \InvalidArgumentException('Générateur mixte non trouvé');

            $pch = $this->moteur_dimensionnement_chauffage->calcule_pch(
                entity: $generateur_mixte,
                simulation: $simulation,
            );
        }

        $pdim = \max($pch, $pecs);;

        return $entity->signaletique()->type_chaudiere ? $this->pn_chaudiere(
            type_chaudiere: $entity->signaletique()->type_chaudiere,
            annee_installation: $entity->annee_installation() ?? $entity->ecs()->audit()->annee_construction_batiment(),
            pdim: $pdim,
        ) : $pdim;
    }

    /**
     * Puissance nominale conventionnelle de la chaudière en kW
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
     * Puissance des auxiliaires de génération en W
     * 
     * @param float $pn - Puissance nominale du générateur en kW
     */
    public function paux(CategorieGenerateur $categorie_generateur, float $pn, ?bool $presence_ventouse,): float
    {
        if (null === $data = $this->paux_repository->find_by(
            categorie_generateur: $categorie_generateur,
            presence_ventouse: $presence_ventouse,
        )) throw new \DomainException("Valeur forfaitaire Paux non trouvée");

        $pn_max = $data->pn_max ? \min($pn, $data->pn_max) : $pn;

        return $data->g + $data->h * $pn_max;
    }

    public function cop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        int $annee_installation,
        ?float $cop_saisi,
    ): float {
        if ($cop_saisi)
            return $cop_saisi;

        if (null === $data = $this->cop_repository->find_by(
            zone_climatique: $zone_climatique,
            type_generateur: $type_generateur,
            annee_installation: $annee_installation,
        )) {
            dd($zone_climatique, $type_generateur, $annee_installation);
            throw new \DomainException("Valeur forfaitaire COP non trouvée");
        }

        return $data->cop;
    }

    public function cop_applicable(TypeGenerateur $type_generateur,): bool
    {
        return \in_array($type_generateur, [
            TypeGenerateur::PAC_DOUBLE_SERVICE,
            TypeGenerateur::CET_AIR_AMBIANT,
            TypeGenerateur::CET_AIR_EXTERIEUR,
            TypeGenerateur::CET_AIR_EXTRAIT,
        ]);
    }

    /**
     * @return array{rpn: float, qp0: float, pveilleuse: float}
     * 
     * @key rpn => Rendement à pleine charge en %
     * @key qp0 => Pertes à l'arrêt du générateur en W
     * @key pveilleuse => Puissance de la veilleuse en W
     */
    public function combustion(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        int $annee_installation,
        float $pn,
        ?bool $presence_ventouse,
        ?float $rpn_saisi,
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
        $qp0 = $qp0_saisi ?? $this->expression_resolver->evalue(expression: $data->qp0, variables: $variables);
        $pveilleuse = $pveilleuse_saisi ?? ($data->pveilleuse ? $this->expression_resolver->evalue(expression: $data->pveilleuse, variables: $variables) : 0);

        return ['rpn' => $rpn, 'qp0' => $qp0, 'pveilleuse' => $pveilleuse];
    }

    public function combustion_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_BOIS,
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
            CategorieGenerateur::POELE_BOIS_BOUILLEUR,
            CategorieGenerateur::CHAUFFE_EAU_INSTANTANE,
        ]);
    }
}
