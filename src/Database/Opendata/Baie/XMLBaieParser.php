<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Baie\{Baie, BaieCollection};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\MasqueProche\MasqueProcheCollection;
use App\Domain\Baie\Enum\Mitoyennete;
use App\Domain\Common\Identifier\Reference;

final class XMLBaieParser
{
    public function __construct(private XMLBaieReader $reader)
    {
    }

    public function parse(XMLElement $xml, Enveloppe $enveloppe): BaieCollection
    {
        foreach ($this->reader->read($xml) as $reader) {
            for ($i = 1; $i <= $reader->nombre(); $i++) {
                $entity = new Baie(
                    id: $reader->id(),
                    enveloppe: $enveloppe,
                    description: $reader->description(),
                    mitoyennete: $reader->mitoyennete(),
                    orientation: $reader->orientation(),
                    caracteristique: $reader->caracteristique(),
                    double_fenetre: $reader->double_fenetre_reader()?->double_fenetre(),
                    masque_proche_collection: new MasqueProcheCollection,
                    paroi_opaque: null,
                    local_non_chauffe: null
                );
                $this->set_mitoyennete(reader: $reader, entity: $entity);
                $this->set_paroi_opaque(reader: $reader, entity: $entity);
                $enveloppe->baie_collection()->add($entity);
            }
        }
        return $enveloppe->baie_collection();
    }

    protected function set_paroi_opaque(XMLBaieReader $reader, Baie &$entity): void
    {
        if (null === $reference_paroi = $reader->reference_paroi()) {
            return;
        }
        $entity->set_paroi_opaque(Reference::create($reference_paroi));
    }

    protected function set_mitoyennete(XMLBaieReader $reader, Baie &$entity): void
    {
        // Local non chauffé référencée - Espace tampon solarisé
        if ($reference_lnc = $reader->reference_lnc()) {
            if (null === $lnc = $entity->enveloppe()->lnc_collection()->find(Reference::create($reference_lnc))) {
                throw new \Exception("Local non chauffé introuvable");
            }
            $entity->set_local_non_chauffe($lnc->id());
            return;
        }
        // Déduction de la mitoyenneté
        if (Mitoyennete::LOCAL_NON_CHAUFFE === $mitoyennete = Mitoyennete::from_type_adjacence_id($reader->enum_type_adjacence_id())) {
            if (null === $type_lnc = TypeLnc::try_from_type_adjacence_id($reader->enum_type_adjacence_id())) {
                throw new \Exception("Type de local non chauffé introuvable pour le type d'adjacence {$reader->enum_type_adjacence_id()}");
            }
            /** @var ?Lnc */
            $lnc = $entity
                ->enveloppe()
                ->lnc_collection()
                ->search_by_type_lnc($type_lnc)
                ->search_by_surface_paroi($reader->surface_aue())
                ->first();

            if (null === $lnc) {
                throw new \Exception("Local non chauffé introuvable");
            }
            $entity->set_local_non_chauffe($lnc->id());
        } else {
            $entity->set_mitoyennete($mitoyennete);
        }
    }
}
