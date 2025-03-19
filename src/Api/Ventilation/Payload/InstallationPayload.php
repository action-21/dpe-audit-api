<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Common\ValueObject\Id;
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
        #[Assert\All([new Assert\Type(SystemePayload::class,)])]
        #[Assert\Valid]
        #[Assert\Count(min: 1)]
        public array $systemes,
    ) {}


    public function id(): Id
    {
        return Id::from($this->id);
    }
}
