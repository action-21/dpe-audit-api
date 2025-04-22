<?php

namespace App\Domain\Ecs\Engine\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Ecs\Engine\Dimensionnement\DimensionnementGenerateur;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Service\EcsTableValeurRepository;

final class PerformanceGenerateurCombustion extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly EcsTableValeurRepository $table_repository,) {}

    /**
     * Puissance nominale du générateur exprimée en kW
     * 
     * @see \App\Domain\Ecs\Engine\DimensionnementGenerateur::pecs()
     */
    public function pn(): float
    {
        return $this->generateur->signaletique()->pn ?? $this->generateur->data()->pecs;
    }

    /**
     * Rendement à pleine charge exprimée en %
     */
    public function rpn(): ?Pourcentage
    {
        return $this->get("rpn", function () {
            if ($this->generateur->combustion()?->rpn) {
                return $this->generateur->combustion()->rpn;
            }
            if (null === $rpn = $this->table_repository->rpn(
                type_generateur: $this->generateur->type(),
                energie_generateur: $this->generateur->energie(),
                mode_combustion: $this->generateur->combustion()->mode_combustion,
                annee_installation: $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                pn: $this->pn(),
            )) {
                throw new \DomainException("Valeurs forfaitaires Rpn non trouvées");
            }
            return $rpn;
        });
    }

    /**
     * Pertes à l'arrêt du générateur exprimée en W
     */
    public function qp0(): ?float
    {
        return $this->get("qp0", function () {
            if ($this->generateur->combustion()?->qp0) {
                return $this->generateur->combustion()->qp0;
            }
            $e = $this->generateur->combustion()?->presence_ventouse ? 1.75 : 2.5;
            $f = $this->generateur->combustion()?->presence_ventouse ? -0.55 : -0.8;

            if (null === $qp0 = $this->table_repository->qp0(
                type_generateur: $this->generateur->type(),
                energie_generateur: $this->generateur->energie(),
                mode_combustion: $this->generateur->combustion()->mode_combustion,
                annee_installation: $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                pn: $this->pn(),
                e: $e,
                f: $f,
            )) {
                throw new \DomainException("Valeurs forfaitaires QP0 non trouvées");
            }
            return $qp0;
        });
    }

    /**
     * Puissance de la veilleuse exprimée en W
     */
    public function pveilleuse(): ?float
    {
        return $this->get("pveilleuse", function () {
            if ($this->generateur->combustion()?->pveilleuse) {
                return $this->generateur->combustion()->pveilleuse;
            }
            if (null === $pveilleuse = $this->table_repository->pveilleuse(
                type_generateur: $this->generateur->type(),
                energie_generateur: $this->generateur->energie(),
                mode_combustion: $this->generateur->combustion()->mode_combustion,
                annee_installation: $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction,
            )) {
                throw new \DomainException("Valeurs forfaitaires Pveil non trouvées");
            }
            return $pveilleuse;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->generateurs() as $generateur) {
            if (false === $generateur->type()->is_combustion()) {
                continue;
            }
            if (false === $generateur->energie()->is_combustible()) {
                continue;
            }
            if ($generateur->position()->generateur_multi_batiment) {
                continue;
            }
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                rpn: $this->rpn(),
                qp0: $this->qp0(),
                pveilleuse: $this->pveilleuse(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [DimensionnementGenerateur::class];
    }
}
