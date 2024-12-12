<?php

namespace App\Api\Chauffage\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property string[] $emetteurs
 */
final class SystemePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public string $generateur_id,
        #[Assert\Valid]
        public ?ReseauPayload $reseau,
        /** @var string[] */
        #[Assert\All([new Assert\Type('string'), new Assert\Uuid])]
        public array $emetteurs,
    ) {}
}
