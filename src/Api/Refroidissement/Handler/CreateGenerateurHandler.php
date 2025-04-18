<?php

namespace App\Api\Refroidissement\Handler;

use App\Api\Refroidissement\Model\Generateur as Payload;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Factory\GenerateurFactory;
use App\Domain\Refroidissement\Refroidissement;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property GenerateurFactory[] $factories
 */
final class CreateGenerateurHandler
{
    public function __construct(
        #[AutowireIterator(GenerateurFactory::class)]
        private readonly iterable $factories,
    ) {}

    public function __invoke(Payload $payload, Refroidissement $entity): Generateur
    {
        if (null === $factory = $this->factory($payload, $entity)) {
            throw new \RuntimeException('No factory found for the given payload.');
        }
        if ($payload->seer) {
            $factory->set_seer($payload->seer);
        }
        if ($payload->reseau_froid_id) {
            $factory->set_reseau_froid(Id::from($payload->reseau_froid_id));
        }
        return Generateur::create($factory);
    }

    private function factory(Payload $payload, Refroidissement $entity): ?GenerateurFactory
    {
        foreach ($this->factories as $factory) {
            if (false === $factory::supports($payload->type, $payload->energie)) {
                continue;
            }
            return $factory->initialize(
                id: Id::from($payload->id),
                refroidissement: $entity,
                description: $payload->description,
                type: $payload->type,
                energie: $payload->energie,
                annee_installation: $payload->annee_installation
                    ? Annee::from($payload->annee_installation)
                    : null,
            );
        }
        return null;
    }
}
