<?php

namespace App\Api\Refroidissement\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property SystemePayload[] $systemes
 */
final class InstallationPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $surface,
        /** @var SystemePayload[] */
        #[Assert\All([new Assert\Type(SystemePayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $systemes,
    ) {}
}
