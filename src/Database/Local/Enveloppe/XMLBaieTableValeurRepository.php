<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Common\Functions;
use App\Domain\Common\ValueObject\{Inclinaison, Pourcentage};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, SecteurChampsVision, TypeBaie, TypeFermeture, TypeMasqueLointain, TypeMasqueProche, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Enum\TypePose;
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Database\Local\{XMLTableElement, XMLTableDatabase};

final class XMLBaieTableValeurRepository extends XMLParoiTableValeurRepository implements BaieTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?Inclinaison $inclinaison_vitrage,
        ?float $epaisseur_lame_air
    ): ?float {
        $records = $this->db->repository('baie.ug')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('type_vitrage', $type_vitrage)
            ->and('nature_gaz_lame', $nature_gaz_lame)
            ->andCompareTo('inclinaison_vitrage', $inclinaison_vitrage?->value())
            ->getMany()
            ->usort('epaisseur_lame_air', $epaisseur_lame_air)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('ug');
        }
        return Functions::interpolation_lineaire(
            x: $epaisseur_lame_air,
            x1: $records->first()->floatval('epaisseur_lame_air'),
            x2: $records->last()->floatval('epaisseur_lame_air'),
            y1: $records->first()->floatval('ug'),
            y2: $records->last()->floatval('ug')
        );
    }

    public function uw(
        float $ug,
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique
    ): ?float {
        $records = $this->db->repository('baie.uw')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('presence_soubassement', $presence_soubassement)
            ->and('materiau', $materiau)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->getMany()
            ->usort('ug', $ug)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('uw');
        }
        return Functions::interpolation_lineaire(
            x: $ug,
            x1: $records->first()->floatval('ug'),
            x2: $records->last()->floatval('ug'),
            y1: $records->first()->floatval('uw'),
            y2: $records->last()->floatval('uw')
        );
    }

    public function deltar(TypeFermeture $type_fermeture): ?float
    {
        return $this->db->repository('baie.deltar')
            ->createQuery()
            ->and('type_fermeture', $type_fermeture)
            ->getOne()
            ?->floatval('deltar');
    }

    public function ujn(float $deltar, float $uw): ?float
    {
        $records = $this->db->repository('baie.ujn')
            ->createQuery()
            ->and('deltar', $deltar)
            ->getMany()
            ->usort('uw', $uw)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('ujn');
        }
        return Functions::interpolation_lineaire(
            x: $uw,
            x1: $records->first()->floatval('uw'),
            x2: $records->last()->floatval('uw'),
            y1: $records->first()->floatval('ujn'),
            y2: $records->last()->floatval('ujn')
        );
    }

    public function sw(
        TypeBaie $type_baie,
        TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage
    ): ?Pourcentage {
        return $this->db->repository('baie.sw')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('type_pose', $type_pose)
            ->and('presence_soubassement', $presence_soubassement)
            ->and('materiau', $materiau)
            ->and('type_vitrage', $type_vitrage)
            ->and('type_survitrage', $type_survitrage)
            ->getOne()
            ?->to(fn(XMLTableElement $record) => Pourcentage::from_decimal($record->floatval('sw')));
    }

    public function c1(
        Mois $mois,
        ZoneClimatique $zone_climatique,
        Inclinaison $inclinaison,
        ?Orientation $orientation
    ): ?float {
        $records = $this->db->repository('paroi.c1')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('orientation', $orientation)
            ->andCompareTo('inclinaison', $inclinaison->value())
            ->getMany();

        return $records->find(name: 'mois', value: $mois->value)?->floatval('c1');
    }

    public function fe1(
        TypeMasqueProche $type_masque_proche,
        ?Orientation $orientation,
        ?float $avancee_masque
    ): ?float {
        return $this->db->repository('baie.fe1')
            ->createQuery()
            ->and('type_masque_proche', $type_masque_proche)
            ->andCompareTo('avancee_masque', $avancee_masque)
            ->getOne()
            ?->floatval('fe1');
    }

    public function fe2(
        TypeMasqueLointain $type_masque_lointain,
        ?Orientation $orientation,
        ?float $hauteur_masque_alpha,
    ): ?float {
        return $this->db->repository('baie.fe2')
            ->createQuery()
            ->and('type_masque_lointain', $type_masque_lointain)
            ->and('orientation', $orientation)
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne()
            ?->floatval('fe2');
    }

    public function omb(
        TypeMasqueLointain $type_masque_lointain,
        SecteurChampsVision $secteur,
        ?Orientation $orientation,
        ?float $hauteur_masque_alpha,
    ): ?float {
        return $this->db->repository('baie.omb')
            ->createQuery()
            ->and('type_masque_lointain', $type_masque_lointain)
            ->and('secteur', $secteur)
            ->and('orientation', $orientation)
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne()
            ?->floatval('omb');
    }
}
