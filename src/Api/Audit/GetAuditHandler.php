<?php

namespace App\Api\Audit;

use App\Domain\Audit\Audit;
use App\Domain\Audit\AuditRepository;
use App\Domain\Common\Type\Id;

final class GetAuditHandler
{
    public function __construct(private AuditRepository $repository) {}

    public function __invoke(Id $id): ?Audit
    {
        return $this->repository->find($id);
    }
}
