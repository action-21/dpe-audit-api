<?php

namespace App\Application\Audit\Get;

use App\Domain\Audit\Audit;
use App\Domain\Audit\AuditRepository;

final class GetHandler
{
    public function __construct(
        private AuditRepository $repository
    ) {
    }

    public function __invoke(GetQuery $query): ?Audit
    {
        return $this->repository->find($query->toReference());
    }
}
