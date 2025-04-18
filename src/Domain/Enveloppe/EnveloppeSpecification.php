<?php

namespace App\Domain\Enveloppe;

use App\Domain\Audit\Audit;
use App\Domain\Common\SpecificationRule;
use Webmozart\Assert\Assert;

final class EnveloppeSpecification extends SpecificationRule
{
    public function validate_niveaux(Audit $entity): void
    {
        Assert::greaterThan($entity->enveloppe()->niveaux()->count(), 0);
    }

    public function validate_baies(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            Assert::nullOrGreaterThanEq(
                $baie->annee_installation()?->value(),
                $entity->batiment()->annee_construction,
            );
        }
    }

    public function validate_murs(Audit $entity): void
    {
        foreach ($entity->enveloppe()->murs() as $mur) {
            Assert::nullOrGreaterThanEq(
                $mur->annee_construction()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $mur->annee_renovation()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $mur->isolation()->annee_isolation?->value(),
                $entity->batiment()->annee_construction,
            );
        }
    }

    public function validate_planchers_bas(Audit $entity): void
    {
        foreach ($entity->enveloppe()->planchers_bas() as $plancher) {
            Assert::nullOrGreaterThanEq(
                $plancher->annee_construction()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $plancher->annee_renovation()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $plancher->isolation()->annee_isolation?->value(),
                $entity->batiment()->annee_construction,
            );
        }
    }

    public function validate_planchers_hauts(Audit $entity): void
    {
        foreach ($entity->enveloppe()->planchers_hauts() as $plancher) {
            Assert::nullOrGreaterThanEq(
                $plancher->annee_construction()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $plancher->annee_renovation()?->value(),
                $entity->batiment()->annee_construction,
            );
            Assert::nullOrGreaterThanEq(
                $plancher->isolation()->annee_isolation?->value(),
                $entity->batiment()->annee_construction,
            );
        }
    }

    public function validate_portes(Audit $entity): void
    {
        foreach ($entity->enveloppe()->portes() as $porte) {
            Assert::nullOrGreaterThanEq(
                $porte->annee_installation()?->value(),
                $entity->batiment()->annee_construction,
            );
        }
    }

    public function validate(Audit $entity): void
    {
        $this->validate_niveaux($entity);
        $this->validate_baies($entity);
        $this->validate_murs($entity);
        $this->validate_planchers_bas($entity);
        $this->validate_planchers_hauts($entity);
        $this->validate_portes($entity);
    }
}
