<?php

namespace App\Domain\Chauffage\Engine\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Common\ValueObject\Pourcentage;

final class PerformanceGenerateurCombustion extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Chauffage\Engine\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->generateur->data()->pn;
    }

    /**
     * Année d'installation
     */
    public function annee_installation(): Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Type de générateur
     */
    public function type_generateur(): TypeGenerateur
    {
        return $this->generateur->type()->is_pac_hybride() ? TypeGenerateur::CHAUDIERE : $this->generateur->type();
    }

    /**
     * Energie du générateur
     */
    public function energie_generateur(): EnergieGenerateur
    {
        return $this->generateur->energie_partie_chaudiere() ?? $this->generateur->energie();
    }

    /**
     * Rendement à pleine charge
     */
    public function rpn(): Pourcentage
    {
        if ($this->generateur->combustion()->rpn) {
            return $this->generateur->combustion()->rpn;
        }
        if (null === $rpn = $this->table_repository->rpn(
            type_generateur: $this->type_generateur(),
            energie_generateur: $this->energie_generateur(),
            mode_combustion: $this->generateur->combustion()->mode_combustion,
            annee_installation_generateur: $this->annee_installation(),
            pn: $this->pn(),
        )) {
            throw new \DomainException('Valeur forfaitaire Rpn non trouvée');
        }
        return $rpn;
    }

    /**
     * Rendement à charge intermédiaire
     */
    public function rpint(): Pourcentage
    {
        if ($this->generateur->combustion()->rpint) {
            return $this->generateur->combustion()->rpint;
        }
        if (null === $rpint = $this->table_repository->rpint(
            type_generateur: $this->type_generateur(),
            energie_generateur: $this->energie_generateur(),
            mode_combustion: $this->generateur->combustion()->mode_combustion,
            annee_installation_generateur: $this->annee_installation(),
            pn: $this->pn(),
        )) {
            throw new \DomainException('Valeur forfaitaire Rpint non trouvée');
        }
        return $rpint;
    }

    /**
     * Pertes à l'arrêt exprimées en W
     */
    public function qp0(): float
    {
        $e = $this->generateur->combustion()->presence_ventouse ? 1.75 : 2.5;
        $f = $this->generateur->combustion()->presence_ventouse ? -0.55 : -0.8;

        if (null === $qp0 = $this->table_repository->qp0(
            type_generateur: $this->type_generateur(),
            energie_generateur: $this->energie_generateur(),
            mode_combustion: $this->generateur->combustion()->mode_combustion,
            annee_installation_generateur: $this->annee_installation(),
            pn: $this->pn(),
            e: $e,
            f: $f,
        )) {
            throw new \DomainException('Valeur forfaitaire QP0 non trouvée');
        }
        return $qp0;
    }

    /**
     * Puissance de la veilleuse exprimée en W
     */
    public function pveilleuse(): float
    {
        if (null === $pveilleuse = $this->table_repository->pveilleuse(
            type_generateur: $this->type_generateur(),
            energie_generateur: $this->energie_generateur(),
            mode_combustion: $this->generateur->combustion()->mode_combustion,
            annee_installation_generateur: $this->annee_installation(),
            pn: $this->pn(),
        )) {
            throw new \DomainException('Valeur forfaitaire Pveilleuse non trouvée');
        }
        return $pveilleuse;
    }

    public static function supports(Generateur $generateur): bool
    {
        $type_generateur = $generateur->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            : $generateur->type();

        $energie_generateur = $generateur->type()->is_pac_hybride()
            ? $generateur->energie_partie_chaudiere()
            : $generateur->energie();

        return in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE,
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::RADIATEUR_GAZ,
            TypeGenerateur::POELE_BOUILLEUR,
        ]) && $energie_generateur->is_combustible();
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            if (false === static::supports($generateur)) {
                continue;
            }
            $this->generateur = $generateur;

            $generateur->calcule($generateur->data()->with(
                rpn: $this->rpn(),
                rpint: $this->rpint(),
                qp0: $this->qp0(),
                pveilleuse: $this->pveilleuse(),
            ));
        }
    }
}
