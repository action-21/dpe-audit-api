<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};
use App\Domain\PontThermique\ValueObject\Liaison;

final class XMLPontThermiqueTransformer
{
    public function __construct(private XMLPontThermiqueReader $reader) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PontThermiqueCollection
    {
        foreach ($this->reader->read($root->pont_thermique_collection()) as $reader) {
            if (null === $reader->id_paroi_1() || null === $reader->id_paroi_2())
                continue;

            $mur = null;
            $plancher = null;
            $ouverture = null;

            if ($reader->id_paroi_1()) {
                $mur = $enveloppe->parois()->murs()->find($reader->id_paroi_1());
                $plancher = $enveloppe->parois()->planchers_bas()->find($reader->id_paroi_1());
                $plancher = $plancher ?? $enveloppe->parois()->planchers_hauts()->find($reader->id_paroi_1());
                $ouverture = $enveloppe->parois()->baies()->find($reader->id_paroi_1())
                    ?? $enveloppe->parois()->portes()->find($reader->id_paroi_1());
            }
            if ($reader->id_paroi_2()) {
                $mur = $mur ?? $enveloppe->parois()->murs()->find($reader->id_paroi_2());
                $plancher = $plancher ?? $enveloppe->parois()->planchers_bas()->find($reader->id_paroi_2());
                $plancher = $plancher ?? $enveloppe->parois()->planchers_hauts()->find($reader->id_paroi_2());
                $ouverture = $ouverture ?? (
                    $enveloppe->parois()->baies()->find($reader->id_paroi_2())
                    ?? $enveloppe->parois()->portes()->find($reader->id_paroi_2())
                );
            }
            $liaison = match ($reader->type_liaison()) {
                TypeLiaison::PLANCHER_BAS_MUR => Liaison::create_liaison_plancher_bas_mur(
                    mur_id: $mur->id(),
                    plancher_bas_id: $plancher->id(),
                ),
                TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR => Liaison::create_liaison_plancher_intermediaire_mur(
                    mur_id: $mur->id(),
                    pont_thermique_partiel: $reader->pont_thermique_partiel()
                ),
                TypeLiaison::PLANCHER_HAUT_MUR => Liaison::create_liaison_plancher_haut_mur(
                    mur_id: $mur->id(),
                    plancher_haut_id: $plancher->id(),
                ),
                TypeLiaison::REFEND_MUR => Liaison::create_liaison_refend_mur(
                    mur_id: $mur->id(),
                    pont_thermique_partiel: $reader->pont_thermique_partiel()
                ),
                TypeLiaison::MENUISERIE_MUR => Liaison::create_liaison_menuiserie_mur(
                    mur_id: $mur->id(),
                    ouverture_id: $ouverture->id(),
                ),
            };
            $entity = new PontThermique(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                longueur: $reader->longueur(),
                liaison: $liaison,
                kpt: $reader->k_saisi(),
            );
            $enveloppe->add_pont_thermique($entity);
        }
        return $enveloppe->ponts_thermiques();
    }
}
