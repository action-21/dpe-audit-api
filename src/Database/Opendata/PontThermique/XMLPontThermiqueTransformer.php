<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};
use App\Domain\PontThermique\ValueObject\Liaison;

final class XMLPontThermiqueTransformer
{
    public function transform(XMLElement $root, Enveloppe $enveloppe): PontThermiqueCollection
    {
        foreach ($root->read_enveloppe()->read_ponts_thermiques() as $reader) {
            $mur_id = $reader->mur_id();
            $plancher_id = $reader->plancher_id();
            $ouverture_id = $reader->ouverture_id();

            $liaison = match ($reader->type_liaison()) {
                TypeLiaison::PLANCHER_BAS_MUR => Liaison::create_liaison_plancher_bas_mur(
                    mur_id: $mur_id,
                    plancher_bas_id: $plancher_id,
                ),
                TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR => Liaison::create_liaison_plancher_intermediaire_mur(
                    mur_id: $mur_id,
                    pont_thermique_partiel: $reader->pont_thermique_partiel()
                ),
                TypeLiaison::PLANCHER_HAUT_MUR => Liaison::create_liaison_plancher_haut_mur(
                    mur_id: $mur_id,
                    plancher_haut_id: $plancher_id,
                ),
                TypeLiaison::REFEND_MUR => Liaison::create_liaison_refend_mur(
                    mur_id: $mur_id,
                    pont_thermique_partiel: $reader->pont_thermique_partiel()
                ),
                TypeLiaison::MENUISERIE_MUR => Liaison::create_liaison_menuiserie_mur(
                    mur_id: $mur_id,
                    ouverture_id: $ouverture_id,
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
