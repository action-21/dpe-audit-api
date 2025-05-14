<?php

namespace App\Serializer\Opendata;

use App\Database\Opendata\Chauffage\XMLChauffageReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\{Emetteur, Installation, Systeme};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\Repository\ReseauChaleurRepository;
use Webmozart\Assert\Assert;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property iterable<GenerateurFactory> $factories
 */
final class XMLChauffageDeserializer
{
    private XMLChauffageReader $reader;
    private Chauffage $entity;

    public function __construct(
        private readonly ReseauChaleurRepository $reseau_chaleur_repository,
        #[AutowireIterator('app.chauffage.generateur.factory')]
        private readonly iterable $factories,
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
            $entity = null;

            foreach ($this->factories as $factory) {
                if (false === $factory->supports(
                    type: $reader->type_generateur(),
                    energie: $reader->energie_generateur(),
                )) {
                    continue;
                }
                $factory->initialize(
                    id: $reader->id(),
                    chauffage: $this->entity,
                    description: $reader->description(),
                    type: $reader->type_generateur(),
                    energie: $reader->energie_generateur(),
                    annee_installation: $reader->annee_installation(),
                );

                $factory->set_position(
                    generateur_collectif: $reader->generateur_collectif(),
                    position_volume_chauffe: $reader->position_volume_chauffe(),
                    generateur_multi_batiment: $reader->generateur_multi_batiment(),
                    generateur_mixte_id: $reader->generateur_mixte_id(),
                );

                $factory->set_signaletique($reader->signaletique());

                if ($reader->reseau_chaleur_id()) {
                    $factory->set_reseau_chaleur($reader->reseau_chaleur_id());
                }

                $entity = $factory->build();
                break;
            }

            if (null === $entity) {
                throw new \RuntimeException('No factory supports the given type and energy.');
            }
            $this->entity->add_generateur($factory->build());
        }
    }

    private function deserialize_emetteurs(): void
    {
        foreach ($this->reader->emetteurs() as $reader) {
            $this->entity->add_emetteur(Emetteur::create(
                id: $reader->id(),
                chauffage: $this->entity,
                description: $reader->description(),
                type: $reader->type_emetteur(),
                temperature_distribution: $reader->temperature_distribution(),
                presence_robinet_thermostatique: $reader->presence_robinet_thermostatique(),
                annee_installation: $reader->annee_installation(),
            ));
        }
    }

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $this->entity->add_installation(Installation::create(
                id: $reader->id(),
                chauffage: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                comptage_individuel: $reader->comptage_individuel(),
                solaire_thermique: $reader->solaire_thermique(),
                regulation_centrale: $reader->regulation_centrale(),
                regulation_terminale: $reader->regulation_terminale(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $generateur = $this->entity->generateurs()->find($reader->generateur_id());
            $installation = $this->entity->installations()->find($reader->installation_id());

            Assert::notNull($generateur, "Générateur {$reader->generateur_id()} non trouvé.");
            Assert::notNull($installation, "Installation {$reader->installation_id()} non trouvé.");

            $entity = Systeme::create(
                id: $reader->id(),
                chauffage: $this->entity,
                installation: $installation,
                generateur: $generateur,
                reseau: $reader->reseau(),
            );

            foreach ($reader->emetteurs() as $reader) {
                $emetteur = $this->entity->emetteurs()->find($reader->id());
                Assert::notNull($emetteur, "Emetteur {$reader->id()} non trouvée.");
                $entity->reference_emetteur($emetteur);
            }

            $this->entity->add_systeme($entity);
        }
    }
}
