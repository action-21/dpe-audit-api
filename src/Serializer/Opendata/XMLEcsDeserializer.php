<?php

namespace App\Serializer\Opendata;

use App\Database\Opendata\Ecs\XMLEcsReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\{Installation, Systeme};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\Repository\ReseauChaleurRepository;
use App\Domain\Ecs\ValueObject\{Reseau, Solaire, Stockage};
use Webmozart\Assert\Assert;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property iterable<GenerateurFactory> $factories
 */
final class XMLEcsDeserializer
{
    private XMLEcsReader $reader;
    private Ecs $entity;

    public function __construct(
        private readonly ReseauChaleurRepository $reseau_chaleur_repository,
        #[AutowireIterator('app.ecs.generateur.factory')]
        private readonly iterable $factories,
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
            $entity = null;

            foreach ($this->factories as $factory) {
                if (false === $factory->supports(
                    type: $reader->type(),
                    energie: $reader->energie(),
                )) {
                    continue;
                }
                $factory->initialize(
                    id: $reader->id(),
                    ecs: $this->entity,
                    description: $reader->description(),
                    type: $reader->type(),
                    energie: $reader->energie(),
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

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $solaire_thermique = null;

            if ($reader->usage_solaire()) {
                $solaire_thermique = Solaire::create(
                    usage: $reader->usage_solaire(),
                    annee_installation: $reader->annee_installation_solaire(),
                    fecs: $reader->fecs_saisi(),
                );
            }
            $this->entity->add_installation(Installation::create(
                id: $reader->id(),
                ecs: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                solaire_thermique: $solaire_thermique,
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
                $stockage = Stockage::create(
                    volume: $reader->volume_stockage(),
                    position_volume_chauffe: $reader->position_volume_chauffe_stockage(),
                );
            }

            $this->entity->add_systeme(Systeme::create(
                id: $reader->id(),
                ecs: $this->entity,
                installation: $installation,
                generateur: $generateur,
                reseau: Reseau::create(
                    alimentation_contigue: $reader->alimentation_contigues(),
                    niveaux_desservis: $reader->niveaux_desservis(),
                    isolation: $reader->isolation_reseau(),
                    bouclage: $reader->bouclage_reseau(),
                ),
                stockage: $stockage,
            ));
        }
    }
}
