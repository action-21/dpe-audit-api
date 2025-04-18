<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Common\ValueObject\Inclinaison;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, SecteurChampsVision, TypeBaie};
use App\Domain\Enveloppe\Enum\Baie\{TypeFermeture, TypeMasqueLointain, TypeMasqueProche};
use App\Domain\Enveloppe\Enum\Baie\{TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Enum\TypePose;

interface BaieTableValeurRepository extends ParoiTableValeurRepository
{
    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?Inclinaison $inclinaison_vitrage,
        ?float $epaisseur_lame_air,
    ): ?float;

    public function uw(
        float $ug,
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique,
    ): ?float;

    public function deltar(TypeFermeture $type_fermeture): ?float;

    public function ujn(float $deltar, float $uw): ?float;

    public function sw(
        TypeBaie $type_baie,
        TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
    ): ?Pourcentage;

    public function c1(
        Mois $mois,
        ZoneClimatique $zone_climatique,
        Inclinaison $inclinaison,
        ?Orientation $orientation,
    ): ?float;

    public function fe1(
        TypeMasqueProche $type_masque_proche,
        ?Orientation $orientation,
        ?float $avancee_masque,
    ): ?float;

    public function fe2(
        TypeMasqueLointain $type_masque_lointain,
        ?Orientation $orientation,
        ?float $hauteur_masque_alpha,
    ): ?float;

    public function omb(
        TypeMasqueLointain $type_masque_lointain,
        SecteurChampsVision $secteur,
        ?Orientation $orientation,
        ?float $hauteur_masque_alpha,
    ): ?float;
}
