<?php

namespace App\Domain\Chauffage\Builder;

use App\Domain\Chauffage\Entity\{EmissionCollection, Generateur};
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeChauffage, TypeGenerateur, UsageGenerateur, UtilisationGenerateur};
use App\Domain\Chauffage\InstallationChauffage;
use App\Domain\Chauffage\ValueObject\{AnneeInstallation, Performance, PuissanceNominale, PuissanceVeilleuse, QP0, Rpint, Rpn, Scop};
use App\Domain\Common\ValueObject\Id;
use App\Domain\ReseauChaleur\ReseauChaleur;

final class GenerateurBuilder
{
    private Generateur $entity;

    public function create(
        InstallationChauffage $installation,
        string $description,
        UsageGenerateur $usage_generateur,
        TypeChauffage $type_chauffage,
        UtilisationGenerateur $utilisation,
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie,
        bool $position_volume_chauffe,
        ?AnneeInstallation $annee_installation = null,
    ): void {
        $this->entity = new Generateur(
            id: Id::create(),
            installation: $installation,
            description: $description,
            position_volume_chauffe: $position_volume_chauffe,
            type_generateur: $type_generateur,
            usage_generateur: $usage_generateur,
            type_chauffage: $type_chauffage,
            energie: $energie,
            utilisation: $utilisation,
            annee_installation: $annee_installation,
            performance: new Performance(),
            emission_collection: new EmissionCollection
        );
    }

    public function build_chaudiere(
        bool $presence_ventouse,
        bool $presence_regulation_combustion,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): Generateur {
        return $this->entity->set_chaudiere(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
            presence_regulation_combustion: $presence_regulation_combustion,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function build_chaudiere_bois_charbon(
        bool $presence_ventouse,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): Generateur {
        return $this->entity->set_chaudiere_bois_charbon(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function build_chaudiere_electrique(): Generateur
    {
        return $this->entity->set_chaudiere_electrique(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
        );
    }

    public function build_chaudiere_fioul_gaz(
        bool $presence_ventouse,
        bool $presence_regulation_combustion,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): Generateur {
        return $this->entity->set_chaudiere_fioul_gaz(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
            presence_regulation_combustion: $presence_regulation_combustion,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function build_chaudiere_multi_batiment(): Generateur
    {
        return $this->entity->set_chaudiere_multi_batiment(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
        );
    }

    public function build_chauffage_electrique(): Generateur
    {
        return $this->entity->set_chauffage_electrique(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
        );
    }

    public function build_generate_air_chaud(bool $presence_ventouse): Generateur
    {
        return $this->entity->set_generate_air_chaud(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
        );
    }

    public function build_poele_bois_bouilleur(
        bool $presence_ventouse,
        ?PuissanceNominale $pn = null,
        ?Rpn $rpn = null,
        ?Rpint $rpint = null,
        ?QP0 $qp0 = null,
        ?PuissanceVeilleuse $pveilleuse = null,
    ): Generateur {
        return $this->entity->set_poele_bois_bouilleur(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function build_poele_insert(): Generateur
    {
        return $this->entity->set_poele_insert(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
        );
    }

    public function build_pac(?Scop $scop = null): Generateur
    {
        return $this->entity->set_pac(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            scop: $scop,
        );
    }

    public function build_radiateur_gaz(bool $presence_ventouse): Generateur
    {
        return $this->entity->set_radiateur_gaz(
            type_generateur: $this->entity->type_generateur(),
            energie: $this->entity->energie(),
            presence_ventouse: $presence_ventouse,
        );
    }

    public function build_reseau_chaleur(?ReseauChaleur $reseau_chaleur = null): Generateur
    {
        return $this->entity->set_reseau_chaleur(
            type_generateur: $this->entity->type_generateur(),
            reseau_chaleur: $reseau_chaleur,
        );
    }
}
