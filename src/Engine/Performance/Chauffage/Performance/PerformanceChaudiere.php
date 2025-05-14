<?php

namespace App\Engine\Performance\Chauffage\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\ValueObject\Annee;
use App\Engine\Performance\Rule;

final class PerformanceChaudiere extends Rule
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
     * Type de générateur
     */
    public function type_generateur(): TypeGenerateur
    {
        return $this->generateur->type()->is_pac_hybride() ? TypeGenerateur::CHAUDIERE : $this->generateur->type();
    }

    /**
     * Température de fonctionnement à 30% de charge
     */
    public function tfonc30(): float
    {
        return $this->get("tfonc30", function () {
            if ($this->generateur->combustion()->tfonc30) {
                return $this->generateur->combustion()->tfonc30;
            }
            $tfonc30 = [];

            if (0 === count($this->generateur->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
            }
            foreach ($this->generateur->emetteurs() as $emetteur) {
                if (null === $value = $this->table_repository->tfonc30(
                    type_generateur: $this->type_generateur(),
                    mode_combustion: $this->generateur->combustion()->mode_combustion,
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_emetteur: $emetteur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                    annee_installation_generateur: $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                )) {
                    throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
                }
                $tfonc30[] = $value;
            }
            return max($tfonc30);
        });
    }

    /**
     * Température de fonctionnement à 100% de charge
     */
    public function tfonc100(): float
    {
        return $this->get("tfonc100", function () {
            if ($this->generateur->combustion()->tfonc100) {
                return $this->generateur->combustion()->tfonc100;
            }
            $tfonc100 = [];

            if (0 === count($this->generateur->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
            }
            foreach ($this->generateur->emetteurs() as $emetteur) {
                if (null === $value = $this->table_repository->tfonc100(
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_emetteur: $emetteur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                )) {
                    throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
                }
                $tfonc100[] = $value;
            }
            return max($tfonc100);
        });
    }

    public static function match(Generateur $generateur): bool
    {
        $type_generateur = $generateur->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            : $generateur->type();

        $energie_generateur = $generateur->type()->is_pac_hybride()
            ? $generateur->energie_partie_chaudiere()
            : $generateur->energie();

        return in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE,
        ]) && $energie_generateur->is_combustible();
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            if (false === self::match($generateur)) {
                continue;
            }
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                tfonc30: $this->tfonc30(),
                tfonc100: $this->tfonc100(),
            ));
        }
    }
}
