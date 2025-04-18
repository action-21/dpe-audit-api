<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLElement;
use App\Domain\Ecs\Data\{GenerateurData, InstallationData, SystemeData};
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\{Generateur, Installation, Systeme};
use App\Domain\Ecs\Repository\ReseauChaleurRepository;
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Position, Signaletique};
use App\Domain\Ecs\ValueObject\{Reseau, Solaire, Stockage};
use Webmozart\Assert\Assert;

final class XMLEcsDeserializer
{
    private XMLEcsReader $reader;
    private Ecs $entity;

    public function __construct(
        private readonly ReseauChaleurRepository $reseau_chaleur_repository,
    ) {}

    public function deserialize(XMLElement $xml): Ecs
    {
        $this->reader = XMLEcsReader::from($xml);
        $this->entity = Ecs::create();

        $this->deserialize_generateurs();
        $this->deserialize_installations();
        $this->deserialize_systemes();

        return $this->entity;
    }

    private function deserialize_generateurs(): void
    {
        foreach ($this->reader->generateurs() as $reader) {
            $reseau_chaleur = null;
            $combustion = null;

            if ($reader->reseau_chaleur_id()) {
                $reseau_chaleur = $this->reseau_chaleur_repository->find($reader->reseau_chaleur_id());
                Assert::notNull($reseau_chaleur, "Réseau de chaleur {$reader->reseau_chaleur_id()} non trouvé");
            }
            if ($reader->mode_combustion()) {
                $combustion = new Combustion(
                    mode_combustion: $reader->mode_combustion(),
                    presence_ventouse: $reader->presence_ventouse(),
                    pveilleuse: $reader->pveilleuse(),
                    qp0: $reader->qp0(),
                    rpn: $reader->rpn(),
                );
            }
            $this->entity->add_generateur(new Generateur(
                id: $reader->id(),
                ecs: $this->entity,
                description: $reader->description(),
                type: $reader->type(),
                energie: $reader->energie(),
                usage: $reader->usage(),
                annee_installation: $reader->annee_installation(),
                position: new Position(
                    generateur_collectif: $reader->generateur_collectif(),
                    generateur_multi_batiment: $reader->generateur_multi_batiment(),
                    position_volume_chauffe: $reader->position_volume_chauffe(),
                    generateur_mixte_id: $reader->generateur_mixte_id(),
                    reseau_chaleur: $reseau_chaleur,
                ),
                signaletique: new Signaletique(
                    volume_stockage: $reader->volume_stockage(),
                    type_chaudiere: $reader->type_chaudiere(),
                    label: $reader->label(),
                    pn: $reader->pn(),
                    cop: $reader->cop(),
                    combustion: $combustion,
                ),
                data: GenerateurData::create(),
            ));
        }
    }

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $solaire_thermique = null;

            if ($reader->usage_solaire()) {
                $solaire_thermique = new Solaire(
                    usage: $reader->usage_solaire(),
                    annee_installation: $reader->annee_installation_solaire(),
                    fecs: $reader->fecs_saisi(),
                );
            }
            $this->entity->add_installation(new Installation(
                id: $reader->id(),
                ecs: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                solaire_thermique: $solaire_thermique,
                data: InstallationData::create(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $installation = $this->entity->installations()->find($reader->installation_id());
            $generateur = $this->entity->generateurs()->find($reader->generateur_id());
            $stockage = null;

            Assert::notNull($installation, "Installation {$reader->installation_id()} non trouvée");
            Assert::notNull($generateur, "Générateur {$reader->generateur_id()} non trouvé");

            if ($reader->stockage()) {
                $stockage = new Stockage(
                    volume: $reader->volume_stockage(),
                    position_volume_chauffe: $reader->position_volume_chauffe_stockage(),
                );
            }

            $this->entity->add_systeme(new Systeme(
                id: $reader->id(),
                ecs: $this->entity,
                installation: $installation,
                generateur: $generateur,
                reseau: new Reseau(
                    alimentation_contigue: $reader->alimentation_contigues(),
                    niveaux_desservis: $reader->niveaux_desservis(),
                    isolation: $reader->isolation_reseau(),
                    bouclage: $reader->bouclage_reseau(),
                ),
                stockage: $stockage,
                data: SystemeData::create(),
            ));
        }
    }
}
