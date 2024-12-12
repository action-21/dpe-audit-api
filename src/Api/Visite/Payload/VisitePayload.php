<?php

namespace App\Api\Visite\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property LogementPayload[] $logements
 */
final class VisitePayload
{
    public function __construct(
        /** @var LogementPayload[] */
        #[Assert\All([new Assert\Type(LogementPayload::class)])]
        #[Assert\Valid]
        public array $logements,
    ) {}
}
