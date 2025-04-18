<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Data\PontThermiqueData;
use App\Domain\Enveloppe\Entity\PontThermique;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete, TypeDeperdition, TypeIsolation, TypePose};
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;
use App\Domain\Enveloppe\Service\PontThermiqueTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

final class DeperditionPontThermique extends EngineRule
{
    private Audit $audit;
    private PontThermique $pont_thermique;

    public function __construct(
        private readonly PontThermiqueTableValeurRepository $table_repository,
    ) {}

    public function pont_thermique_negligeable(): bool
    {
        if ($this->pont_thermique->liaison()->mur->pont_thermique_negligeable()) {
            return true;
        }
        if ($this->pont_thermique->liaison()->paroi?->pont_thermique_negligeable()) {
            return true;
        }
        return false;
    }

    public function etat_isolation_mur(): EtatIsolation
    {
        if ($this->pont_thermique->liaison()->mur->isolation()->etat_isolation) {
            return $this->pont_thermique->liaison()->mur->isolation()->etat_isolation;
        }
        return $this->audit->batiment()->annee_construction->less_than(1975)
            ? EtatIsolation::NON_ISOLE
            : EtatIsolation::ISOLE;
    }

    public function type_isolation_mur(): ?TypeIsolation
    {
        if ($this->pont_thermique->liaison()->mur->isolation()->type_isolation) {
            return $this->pont_thermique->liaison()->mur->isolation()->type_isolation;
        }
        return $this->etat_isolation_mur() === EtatIsolation::ISOLE
            ? TypeIsolation::ITI
            : null;
    }

    public function etat_isolation_plancher_bas(): ?EtatIsolation
    {
        if ($this->pont_thermique->liaison()->type !== TypeLiaison::PLANCHER_BAS_MUR) {
            return null;
        }
        if ($this->pont_thermique->liaison()->plancher_bas()->isolation()->etat_isolation) {
            return $this->pont_thermique->liaison()->plancher_bas()->isolation()->etat_isolation;
        }
        if ($this->pont_thermique->liaison()->plancher_bas()->mitoyennete() === Mitoyennete::TERRE_PLEIN) {
            return $this->audit->batiment()->annee_construction->less_than(2001)
                ? EtatIsolation::NON_ISOLE
                : EtatIsolation::ISOLE;
        }
        return $this->audit->batiment()->annee_construction->less_than(1975)
            ? EtatIsolation::NON_ISOLE
            : EtatIsolation::ISOLE;
    }

    public function type_isolation_plancher_bas(): ?EtatIsolation
    {
        if ($this->pont_thermique->liaison()->type !== TypeLiaison::PLANCHER_BAS_MUR) {
            return null;
        }
        if ($this->pont_thermique->liaison()->plancher_bas()->isolation()->type_isolation) {
            return $this->pont_thermique->liaison()->plancher_bas()->isolation()->type_isolation;
        }
        return $this->etat_isolation_plancher_bas() === EtatIsolation::ISOLE
            ? TypeIsolation::ITE
            : null;
    }

    public function etat_isolation_plancher_haut(): ?EtatIsolation
    {
        if ($this->pont_thermique->liaison()->type !== TypeLiaison::PLANCHER_HAUT_MUR) {
            return null;
        }
        if ($this->pont_thermique->liaison()->plancher_haut()->isolation()->etat_isolation) {
            return $this->pont_thermique->liaison()->plancher_haut()->isolation()->etat_isolation;
        }
        return $this->audit->batiment()->annee_construction->less_than(1975)
            ? EtatIsolation::NON_ISOLE
            : EtatIsolation::ISOLE;
    }

    public function type_isolation_plancher_haut(): ?EtatIsolation
    {
        if ($this->pont_thermique->liaison()->type !== TypeLiaison::PLANCHER_HAUT_MUR) {
            return null;
        }
        if ($this->pont_thermique->liaison()->plancher_haut()->isolation()->type_isolation) {
            return $this->pont_thermique->liaison()->plancher_haut()->isolation()->type_isolation;
        }
        return $this->etat_isolation_plancher_haut() === EtatIsolation::ISOLE
            ? TypeIsolation::ITE
            : null;
    }

    public function type_pose(): ?TypePose
    {
        return $this->pont_thermique->liaison()->menuiserie()
            ? $this->pont_thermique->liaison()->menuiserie()->type_pose() ?? TypePose::NU_INTERIEUR
            : null;
    }

    public function presence_retour_isolation(): ?bool
    {
        return $this->pont_thermique->liaison()->menuiserie()
            ? $this->pont_thermique->liaison()->menuiserie()->presence_retour_isolation() ?? false
            : null;
    }

    public function largeur_dormant(): ?bool
    {
        return $this->pont_thermique->liaison()->menuiserie()
            ? $this->pont_thermique->liaison()->menuiserie()->largeur_dormant() ?? 50
            : null;
    }

    /**
     * Valeur du pont thermique en W/K
     */
    public function kpt(): float
    {
        if (null !== $this->pont_thermique->kpt()) {
            return $this->pont_thermique->kpt();
        }
        if (null === $value = $this->table_repository->kpt(
            type_liaison: $this->pont_thermique->liaison()->type,
            etat_isolation_mur: $this->etat_isolation_mur(),
            type_isolation_mur: $this->type_isolation_mur(),
            etat_isolation_plancher: $this->etat_isolation_plancher_bas() ?? $this->etat_isolation_plancher_haut(),
            type_isolation_plancher: $this->type_isolation_plancher_bas() ?? $this->type_isolation_plancher_haut(),
            type_pose: $this->type_pose(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            largeur_dormant: $this->largeur_dormant(),
        )) {
            throw new \DomainException('Valeur forfaitaire kpt non trouvée');
        }
        return $value;
    }

    /**
     * Déperdition thermique en W/K
     */
    public function pt(): float
    {
        if ($this->pont_thermique_negligeable()) {
            return 0;
        }
        $kpt = $this->kpt();
        $longueur = $this->pont_thermique->longueur();
        $pont_thermique_partiel = $this->pont_thermique->liaison()->pont_thermique_partiel;

        return $kpt * $longueur * ($pont_thermique_partiel ? 0.5 : 1);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        foreach ($entity->enveloppe()->ponts_thermiques() as $pont_thermique) {
            $this->pont_thermique = $pont_thermique;
            $pont_thermique->calcule(PontThermiqueData::create(
                kpt: $this->kpt(),
                pt: $this->pt(),
            ));
            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
                type: TypeDeperdition::PONT_THERMIQUE,
                deperdition: $this->pt(),
            )));
        }
    }
}
