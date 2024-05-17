<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeChauffage, TypeGenerateur, UsageGenerateur, UtilisationGenerateur};
use App\Domain\Chauffage\InstallationChauffage;
use App\Domain\Chauffage\ValueObject\{AnneeInstallation, PrioriteCascade, Performance, PuissanceNominale, PuissanceVeilleuse, QP0, Rpint, Rpn, Scop, Surface};
use App\Domain\Common\ValueObject\Id;
use App\Domain\ReseauChaleur\ReseauChaleur;

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly InstallationChauffage $installation,
        private string $description,
        private bool $position_volume_chauffe,
        private TypeGenerateur $type_generateur,
        private UsageGenerateur $usage_generateur,
        private UtilisationGenerateur $utilisation,
        private TypeChauffage $type_chauffage,
        private EnergieGenerateur $energie,
        private Performance $performance,
        private EmissionCollection $emission_collection,
        private ?AnneeInstallation $annee_installation = null,
        private ?PrioriteCascade $priorite_cascade = null,
        private ?ReseauChaleur $reseau_chaleur = null,
    ) {
    }

    public function update(
        string $description,
        UsageGenerateur $usage_generateur,
        UtilisationGenerateur $utilisation,
        TypeChauffage $type_chauffage,
        bool $position_volume_chauffe,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        $this->description = $description;
        $this->usage_generateur = $usage_generateur;
        $this->utilisation = $utilisation;
        $this->type_chauffage = $type_chauffage;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->annee_installation = $annee_installation;
        $this->controle_coherence();
        return $this;
    }

    public function set_chaudiere(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
        bool $presence_regulation_combustion,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        if (false === $type_generateur->autre_chaudiere()) {
            throw new \DomainException('Le type de générateur n\'est pas une chaudière');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(
            presence_regulation_combustion: $presence_regulation_combustion,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_chaudiere_bois_charbon(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        if (false === $type_generateur->chaudiere_bois() && false === $type_generateur->chaudiere_charbon()) {
            throw new \DomainException('Le type de générateur n\'est pas une chaudière bois ou charbon');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_chaudiere_electrique(TypeGenerateur $type_generateur, EnergieGenerateur $energie): self
    {
        if (false === $type_generateur->chaudiere_electrique()) {
            throw new \DomainException('Le type de générateur n\'est pas une chaudière électrique');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance();
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_chaudiere_fioul_gaz(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
        bool $presence_regulation_combustion,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        if (false === $type_generateur->chaudiere_fioul() && false === $type_generateur->chaudiere_gaz()) {
            throw new \DomainException('Le type de générateur n\'est pas une chaudière fioul ou gaz');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(
            presence_regulation_combustion: $presence_regulation_combustion,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_chaudiere_multi_batiment(TypeGenerateur $type_generateur, EnergieGenerateur $energie): self
    {
        if (false === $type_generateur->chaudiere_multi_batiment()) {
            throw new \DomainException('Le type de générateur n\'est pas une chaudière multi-bâtiment');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance();
        $this->position_volume_chauffe = false;
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_chauffage_electrique(TypeGenerateur $type_generateur, EnergieGenerateur $energie): self
    {
        if (false === $type_generateur->chauffage_electrique()) {
            throw new \DomainException('Le type de générateur n\'est pas un chauffage électrique');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance();
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_generate_air_chaud(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
    ): self {
        if (false === $type_generateur->generateur_air_chaud()) {
            throw new \DomainException('Le type de générateur n\'est pas un générateur d\'air chaud');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(presence_ventouse: $presence_ventouse);
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_poele_bois_bouilleur(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        if (false === $type_generateur->poele_bois_bouilleur()) {
            throw new \DomainException('Le type de générateur n\'est pas un poêle bouilleur');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
        $this->reseau_chaleur = null;
        return $this;
    }

    public function set_poele_insert(TypeGenerateur $type_generateur, EnergieGenerateur $energie): self
    {
        if (false === $type_generateur->poele_insert()) {
            throw new \DomainException('Le type de générateur n\'est pas un poêle ou un insert');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance();
        $this->reseau_chaleur = null;
        return $this;
    }

    public function set_pac(TypeGenerateur $type_generateur, EnergieGenerateur $energie, ?Scop $scop = null): self
    {
        if (false === $type_generateur->pac()) {
            throw new \DomainException('Le type de générateur n\'est pas une PAC');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(scop: $scop);
        $this->reseau_chaleur = null;
        return $this;
    }

    public function set_radiateur_gaz(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $presence_ventouse,
    ): self {
        if (false === $type_generateur->radiateur_gaz()) {
            throw new \DomainException('Le type de générateur n\'est pas un radiateur gaz');
        }
        $this->type_generateur = $type_generateur;
        $this->energie = $energie;
        $this->performance = new Performance(presence_ventouse: $presence_ventouse);
        $this->reseau_chaleur = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_reseau_chaleur(TypeGenerateur $type_generateur, ?ReseauChaleur $reseau_chaleur = null): self
    {
        if (false === $type_generateur->reseau_chaleur()) {
            throw new \DomainException('Le type de générateur n\'est pas un réseau de chaleur');
        }
        if ($reseau_chaleur && $type_generateur === TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU) {
            throw new \DomainException('Le type de générateur est un réseau de chaleur non répertorié ou inconnu mais un réseau de chaleur est renseigné');
        }
        $this->type_generateur = $type_generateur;
        $this->reseau_chaleur = $reseau_chaleur;
        $this->type_generateur = $reseau_chaleur ? $type_generateur : TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU;
        $this->energie = EnergieGenerateur::RESEAU_CHAUFFAGE_URBAIN;
        $this->performance = new Performance();
        return $this;
    }

    public function controle_coherence(): void
    {
        $usage_generateur_cases = UsageGenerateur::cases_by_type_generateur($this->type_generateur);
        $energie_cases = EnergieGenerateur::cases_by_type_generateur($this->type_generateur);
        $type_chauffage_cases = TypeChauffage::cases_by_type_generateur($this->type_generateur);

        if (\count($usage_generateur_cases) === 1) {
            $this->usage_generateur = \reset($usage_generateur_cases);
        }
        if (\count($energie_cases) === 1) {
            $this->energie = \reset($energie_cases);
        }
        if (\count($type_chauffage_cases) === 1) {
            $this->type_chauffage = \reset($type_chauffage_cases);
        }
        if (null !== $position_volume_chauffe = $this->type_generateur->position_volume_chauffe()) {
            $this->position_volume_chauffe = $position_volume_chauffe;
        }
        if (!\in_array($this->usage_generateur, $usage_generateur_cases)) {
            throw new \DomainException('L\'usage du générateur n\'est pas compatible avec le type de générateur');
        }
        if (!\in_array($this->energie, $energie_cases)) {
            throw new \DomainException('L\'énergie du générateur n\'est pas compatible avec le type de générateur');
        }
        if (!\in_array($this->type_chauffage, $type_chauffage_cases)) {
            throw new \DomainException('Le type de chauffage n\'est pas compatible avec le type de générateur');
        }
        if ($this->utilisation !== UtilisationGenerateur::BASE && 0 === $this->installation->generateur_collection()->search_by_utilisation(UtilisationGenerateur::BASE)->count()) {
            throw new \DomainException('Un générateur d\'appoint doit être associé à un générateur en Base');
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function installation(): InstallationChauffage
    {
        return $this->installation;
    }

    public function reseau_chaleur(): ?ReseauChaleur
    {
        return $this->reseau_chaleur;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function usage_generateur(): UsageGenerateur
    {
        return $this->usage_generateur;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->type_generateur;
    }

    public function type_chauffage(): TypeChauffage
    {
        return $this->type_chauffage;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function utilisation(): UtilisationGenerateur
    {
        return $this->utilisation;
    }

    public function performance(): Performance
    {
        return $this->performance;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return $this->annee_installation;
    }

    public function priorite_cascade(): ?PrioriteCascade
    {
        return $this->priorite_cascade;
    }

    public function emission_collection(): EmissionCollection
    {
        return $this->emission_collection;
    }

    public function get_emission(Id $id): ?Emission
    {
        return $this->emission_collection->find($id);
    }

    public function add_emission(Emission $entity): self
    {
        $this->emission_collection->add($entity);
        return $this;
    }

    public function remove_emission(Emission $entity): self
    {
        $this->emission_collection->removeElement($entity);
        return $this;
    }
}
