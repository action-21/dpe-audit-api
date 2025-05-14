<?php

namespace App\Serializer\Opendata;

use App\Database\Opendata\Refroidissement\XMLRefroidissementReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Refroidissement\Entity\{Installation, Systeme};
use App\Domain\Refroidissement\Factory\GenerateurFactory;
use App\Domain\Refroidissement\Refroidissement;
use Webmozart\Assert\Assert;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property iterable<GenerateurFactory> $factories
 */
final class XMLRefroidissementDeserializer
{
    private XMLRefroidissementReader $reader;
    private Refroidissement $entity;

    public function __construct(
        #[AutowireIterator('app.refroidissement.generateur.factory')]
        private readonly iterable $factories,
    ) {}

    public function deserialize(XMLElement $xml): Refroidissement
    {
        $this->reader = XMLRefroidissementReader::from($xml);
        $this->entity = Refroidissement::create();

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
                    type: $reader->type_generateur(),
                    energie: $reader->energie_generateur(),
                )) {
                    continue;
                }
                $factory->initialize(
                    id: $reader->id(),
                    refroidissement: $this->entity,
                    description: $reader->description(),
                    type: $reader->type_generateur(),
                    energie: $reader->energie_generateur(),
                    annee_installation: $reader->annee_installation(),
                );

                if ($reader->eer()) {
                    $factory->set_seer($reader->eer());
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
            $this->entity->add_installation(Installation::create(
                id: $reader->id(),
                refroidissement: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $installation = $this->entity->installations()->find(id: $reader->installation_id());
            $generateur = $this->entity->generateurs()->find(id: $reader->generateur_id());

            Assert::notNull($installation, "L'installation {$reader->installation_id()} n'a pas été trouvée.");
            Assert::notNull($generateur, "Le générateur {$reader->generateur_id()} n'a pas été trouvé.");

            $this->entity->add_systeme(Systeme::create(
                id: $reader->id(),
                refroidissement: $this->entity,
                installation: $installation,
                generateur: $generateur,
            ));
        }
    }
}
