<?php

namespace App\Application\Porte\Create;

use App\Domain\Enveloppe\EnveloppeRepository;
use App\Domain\Porte\{PorteBuilder, PorteRepository};

final class CreateHandler
{
    public function __construct(
        private EnveloppeRepository $enveloppe_repository,
        private PorteRepository $porte_repository,
        private PorteBuilder $porte_builder,
    ) {
    }

    public function __invoke(CreateCommand $command): CreateResponse
    {
        if (null === $enveloppe = $this->enveloppe_repository->find(id: $command->enveloppe_id)) {
            throw new \DomainException("Enveloppe {$command->enveloppe_id} introuvable");
        }
        $builder = $this->porte_builder;
        $builder->create(
            enveloppe: $enveloppe,
            description: $command->description,
            caracteristique: $command->caracteristique(),
        );
        if ()
    }
}
