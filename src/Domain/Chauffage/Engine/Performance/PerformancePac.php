<?php

namespace App\Domain\Chauffage\Engine\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Annee;

final class PerformancePac extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * Année d'installation
     */
    public function annee_installation(): Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Coefficient de performance énergétique
     */
    public function scop(): float
    {
        return $this->get("scop", function () {
            if ($this->generateur->signaletique()->scop) {
                return $this->generateur->signaletique()->scop;
            }
            if (0 === $this->generateur->emetteurs()->count()) {
                throw new \DomainException("Valeurs forfaitaires COP non trouvées");
            }
            $scops = [];

            foreach ($this->generateur->emetteurs() as $emetteur) {
                if (null === $scop = $this->table_repository->scop(
                    zone_climatique: $this->audit->adresse()->zone_climatique,
                    type_generateur: $this->generateur->type(),
                    annee_installation_generateur: $this->annee_installation(),
                    type_emission: $emetteur->type_emission(),
                )) {
                    throw new \DomainException("Valeurs forfaitaires SCOP non trouvées");
                }
                $scops[] = $scop;
            }
            return max($scops);
        });
    }

    public static function supports(Generateur $generateur): bool
    {
        return $generateur->type()->is_pac() && false === $generateur->position()->generateur_multi_batiment;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            if (false === self::supports($generateur)) {
                continue;
            }
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                scop: $this->scop(),
            ));
        }
    }
}
