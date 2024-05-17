<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, TypeInstallation, UsageGenerateur};
use App\Domain\Ecs\InstallationEcs;
use App\Domain\Ecs\ValueObject\{AnneeInstallation, Cop, Performance, PuissanceNominale, PuissanceVeilleuse, QP0, Rpn, Stockage};

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly InstallationEcs $installation,
        private ?Id $reseau_chaleur_id,
        private string $description,
        private bool $position_volume_chauffe,
        private TypeGenerateur $type_generateur,
        private UsageGenerateur $usage,
        private EnergieGenerateur $energie,
        private Stockage $stockage,
        private Performance $performance,
        private ?AnneeInstallation $annee_installation,
    ) {
    }

    public static function create_reseau_chaleur(
        InstallationEcs $installation,
        string $description,
        TypeGenerateur $type_generateur,
        UsageGenerateur $usage,
        Stockage $stockage,
        ?Id $reseau_chaleur_id,
        ?AnneeInstallation $annee_installation,
    ): self {
        return (new self(
            id: Id::create(),
            installation: $installation,
            reseau_chaleur_id: $reseau_chaleur_id,
            description: $description,
            position_volume_chauffe: false,
            type_generateur: $type_generateur,
            usage: $usage,
            energie: EnergieGenerateur::RESEAU_CHAUFFAGE_URBAIN,
            stockage: $stockage,
            performance: new Performance(),
            annee_installation: $annee_installation,
        ))->set_reseau_chaleur(
            type_generateur: $type_generateur,
            reseau_chaleur_id: $reseau_chaleur_id,
        );
    }

    public static function create_chauffe_eau_thermodynamique(
        InstallationEcs $installation,
        string $description,
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        UsageGenerateur $usage,
        Stockage $stockage,
        bool $position_volume_chauffe,
        ?AnneeInstallation $annee_installation,
        ?Cop $cop,
    ): self {
        return (new self(
            id: Id::create(),
            installation: $installation,
            reseau_chaleur_id: null,
            description: $description,
            position_volume_chauffe: $position_volume_chauffe,
            type_generateur: $type_generateur,
            usage: $usage,
            energie: $energie,
            stockage: $stockage,
            performance: new Performance(),
            annee_installation: $annee_installation,
        ))->set_chauffe_eau_thermodynamique(
            type_generateur: $type_generateur,
            position_volume_chauffe: $position_volume_chauffe,
            cop: $cop
        );
    }

    public static function create_generateur_combustion(
        InstallationEcs $installation,
        string $description,
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        UsageGenerateur $usage,
        Stockage $stockage,
        bool $position_volume_chauffe,
        ?bool $presence_ventouse,
        ?AnneeInstallation $annee_installation,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        return (new self(
            id: Id::create(),
            installation: $installation,
            reseau_chaleur_id: null,
            description: $description,
            position_volume_chauffe: $position_volume_chauffe,
            type_generateur: $type_generateur,
            usage: $usage,
            energie: $energie,
            stockage: $stockage,
            performance: new Performance(),
            annee_installation: $annee_installation,
        ))->set_generateur_combustion(
            type_generateur: $type_generateur,
            energie: $energie,
            position_volume_chauffe: $position_volume_chauffe,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function update(
        string $description,
        UsageGenerateur $usage,
        Stockage $stockage,
        ?AnneeInstallation $annee_installation,
    ): self {
        $this->description = $description;
        $this->usage = $usage;
        $this->stockage = $stockage;
        $this->annee_installation = $annee_installation;
        $this->controle_coherence();
        return $this;
    }

    public function set_reseau_chaleur(TypeGenerateur $type_generateur, ?Id $reseau_chaleur_id): self
    {
        if (false === $type_generateur->reseau_chaleur()) {
            throw new \DomainException('Le type de générateur n\'est pas un réseau de chaleur');
        }
        if ($reseau_chaleur_id && $type_generateur === TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU) {
            throw new \DomainException('L\'identifiant du réseau de chaleur ne doit pas être renseigné pour un type de générateur non répertorié ou inconnu');
        }
        $this->reseau_chaleur_id = $reseau_chaleur_id;
        $this->type_generateur = $reseau_chaleur_id ? $type_generateur : TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU;
        $this->position_volume_chauffe = false;
        $this->energie = EnergieGenerateur::RESEAU_CHAUFFAGE_URBAIN;
        $this->performance = new Performance();
        $this->controle_coherence();
        return $this;
    }

    public function set_chauffe_eau_thermodynamique(
        TypeGenerateur $type_generateur,
        bool $position_volume_chauffe,
        ?Cop $cop = null
    ): self {
        if (false === $type_generateur->chauffe_eau_thermodynamique()) {
            throw new \DomainException('Le type de générateur n\'est pas un chauffe-eau thermodynamique');
        }
        $this->type_generateur = $type_generateur;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->performance = new Performance(cop: $cop);
        $this->reseau_chaleur_id = null;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->controle_coherence();
        return $this;
    }

    public function set_generateur_combustion(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $position_volume_chauffe,
        ?bool $presence_ventouse,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): self {
        if (false === $type_generateur->generateur_combustion()) {
            throw new \DomainException('Le type de générateur n\'est pas un générateur de combustion');
        }
        if (false === $energie->combustible()) {
            throw new \DomainException('L\'énergie du générateur n\'est pas un combustible');
        }
        $this->type_generateur = $type_generateur;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->performance = new Performance(...[$presence_ventouse, $pn, $rpn, $qp0, $pveilleuse, null]);
        $this->energie = $energie;
        $this->reseau_chaleur_id = null;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        $energie_applicable = EnergieGenerateur::cases_by_type_generateur($this->type_generateur);
        $usage_applicable = UsageGenerateur::cases_by_type_generateur($this->type_generateur);

        if (\count($energie_applicable) === 1) {
            $this->energie = \reset($energie_applicable);
        }
        if (\count($usage_applicable) === 1) {
            $this->usage = \reset($usage_applicable);
        }
        if (!\in_array($this->installation->type_installation(), TypeInstallation::cases_by_type_generateur($this->type_generateur))) {
            throw new \DomainException('Le type de générateur sélectionné n\'est pas applicable au type d\'installation');
        }
        if (!\in_array($this->energie, $energie_applicable)) {
            throw new \DomainException('L\'énergie du générateur n\'est pas applicable pour le type de générateur sélectionné');
        }
        if (!\in_array($this->usage, $usage_applicable)) {
            throw new \DomainException('L\'usage du générateur n\'est pas applicable pour le type de générateur sélectionné');
        }
        if (null === $this->annee_installation && AnneeInstallation::is_requis_by_type_generateur($this->type_generateur)) {
            throw new \DomainException('L\'année d\'installation du générateur est requise.');
        }
        if ($this->annee_installation && $this->annee_installation->valeur() < $this->installation()->logement()->batiment()->annee_construction()->valeur()) {
            throw new \DomainException('L\'année d\'installation du générateur est antérieure à l\'année de construction du bâtiment.');
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function installation(): InstallationEcs
    {
        return $this->installation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function reseau_chaleur_id(): ?\Stringable
    {
        return $this->reseau_chaleur_id;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->type_generateur;
    }

    public function usage(): UsageGenerateur
    {
        return $this->usage;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function stockage(): Stockage
    {
        return $this->stockage;
    }

    public function performance(): Performance
    {
        return $this->performance;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return $this->annee_installation;
    }
}
