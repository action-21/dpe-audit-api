<?php

namespace App\Api\Audit;

use App\Domain\Audit\{Audit, AuditRepository};
use App\Domain\Common\ValueObject\Id;

final class GetAuditHandler
{
    public function __construct(private readonly AuditRepository $repository) {}

    public function __invoke(Id $id): ?Audit
    {
        return $this->repository->find($id);
    }
}
