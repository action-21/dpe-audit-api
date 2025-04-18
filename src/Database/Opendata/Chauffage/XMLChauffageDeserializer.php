<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLElement;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Data\{GenerateurData, InstallationData, SystemeData};
use App\Domain\Chauffage\Entity\{Emetteur, EmetteurCollection, Generateur, Installation, Systeme};
use App\Domain\Chauffage\Repository\ReseauChaleurRepository;
use App\Domain\Chauffage\ValueObject\{Regulation, Reseau, Solaire};
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Position, Signaletique};
use Webmozart\Assert\Assert;

final class XMLChauffageDeserializer
{
    private XMLChauffageReader $reader;
    private Chauffage $entity;

    public function __construct(
        private readonly ReseauChaleurRepository $reseau_chaleur_repository,
    ) {}

    public function deserialize(XMLElement $xml): Chauffage
    {
        $this->reader = XMLChauffageReader::from($xml);
        $this->entity = Chauffage::create();

        $this->deserialize_generateurs();
        $this->deserialize_emetteurs();
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
                    presence_regulation_combustion: $reader->presence_regulation_combustion(),
                    pveilleuse: $reader->pveilleuse_saisi(),
                    qp0: $reader->qp0_saisi(),
                    rpn: $reader->rpn_saisi(),
                    rpint: $reader->rpint_saisi(),
                    tfonc30: $reader->tfonc30_saisi(),
                    tfonc100: $reader->tfonc100_saisi(),
                );
            }
            $this->entity->add_generateur(new Generateur(
                id: $reader->id(),
                chauffage: $this->entity,
                description: $reader->description(),
                type: $reader->type_generateur(),
                energie: $reader->energie_generateur(),
                energie_partie_chaudiere: $reader->energie_partie_chaudiere(),
                annee_installation: $reader->annee_installation(),
                usage: $reader->usage(),
                position: new Position(
                    position_volume_chauffe: $reader->position_volume_chauffe(),
                    generateur_collectif: $reader->generateur_collectif(),
                    generateur_multi_batiment: $reader->generateur_multi_batiment(),
                    generateur_mixte_id: $reader->generateur_mixte_id(),
                    reseau_chaleur: $reseau_chaleur,
                ),
                signaletique: new Signaletique(
                    type_chaudiere: $reader->type_chaudiere(),
                    pn: $reader->pn_saisi(),
                    scop: $reader->scop_saisi(),
                    label: $reader->label(),
                    priorite_cascade: $reader->priorite_cascade(),
                    combustion: $combustion,
                ),
                data: GenerateurData::create(),
            ));
        }
    }

    private function deserialize_emetteurs(): void
    {
        foreach ($this->reader->emetteurs() as $reader) {
            $this->entity->add_emetteur(new Emetteur(
                id: $reader->id(),
                chauffage: $this->entity,
                description: $reader->description(),
                type: $reader->type_emetteur(),
                type_emission: $reader->type_emission(),
                temperature_distribution: $reader->temperature_distribution(),
                presence_robinet_thermostatique: $reader->presence_robinet_thermostatique(),
                annee_installation: $reader->annee_installation(),
            ));
        }
    }

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $this->entity->add_installation(new Installation(
                id: $reader->id(),
                chauffage: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                comptage_individuel: $reader->comptage_individuel(),
                solaire_thermique: $reader->usage_solaire() ? new Solaire(
                    usage: $reader->usage_solaire(),
                    annee_installation: $reader->annee_installation_solaire(),
                    fch: $reader->fch_saisi(),
                ) : null,
                regulation_centrale: new Regulation(
                    presence_regulation: $reader->presence_regulation_centrale(),
                    minimum_temperature: $reader->regulation_centrale_minimum_temperature(),
                    detection_presence: $reader->regulation_centrale_detection_presence(),
                ),
                regulation_terminale: new Regulation(
                    presence_regulation: $reader->presence_regulation_terminale(),
                    minimum_temperature: $reader->regulation_terminale_minimum_temperature(),
                    detection_presence: $reader->regulation_terminale_detection_presence(),
                ),
                data: InstallationData::create(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $generateur = $this->entity->generateurs()->find($reader->generateur_id());
            $installation = $this->entity->installations()->find($reader->installation_id());
            $emetteurs = new EmetteurCollection;

            Assert::notNull($generateur, "Générateur {$reader->generateur_id()} non trouvé.");
            Assert::notNull($installation, "Installation {$reader->installation_id()} non trouvé.");

            foreach ($reader->emetteurs() as $emetteur_reader) {
                $emetteur = $this->entity->emetteurs()->find($emetteur_reader->id());
                Assert::notNull($emetteur, "Emetteur {$emetteur_reader->id()} non trouvée.");
                $emetteurs->add($emetteur);
            }

            $this->entity->add_systeme(new Systeme(
                id: $reader->id(),
                chauffage: $this->entity,
                installation: $installation,
                generateur: $generateur,
                type_chauffage: $reader->type_chauffage(),
                reseau: $reader->type_distribution() ? new Reseau(
                    type_distribution: $reader->type_distribution(),
                    isolation: $reader->isolation_reseau(),
                    niveaux_desservis: $reader->niveaux_desservis(),
                    presence_circulateur_externe: $reader->presence_circulateur_externe(),
                ) : null,
                emetteurs: $emetteurs,
                data: SystemeData::create(),
            ));
        }
    }
}
