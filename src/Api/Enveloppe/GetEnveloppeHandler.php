<?php

namespace App\Api\Enveloppe;

use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};
use App\Domain\Common\ValueObject\Id;

final class GetEnveloppeHandler
{
    public function __construct(private EnveloppeRepository $repository) {}

    public function __invoke(Id $id): ?Enveloppe
    {
        return $this->repository->find($id);
    }
}
