<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Refroidissement\Refroidissement as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property Generateur[] $generateurs
 * @property Installation[] $installations
 * @property Systeme[] $systemes
 */
final class Refroidissement
{
    public function __construct(

        /** @var Generateur[] */
        #[Assert\All([new Assert\Type(Generateur::class)])]
        #[Assert\Valid]
        public readonly array $generateurs,

        /** @var Installation[] */
        #[Assert\All([new Assert\Type(Installation::class)])]
        #[Assert\Valid]
        public readonly array $installations,

        /** @var Systeme[] */
        #[Assert\All([new Assert\Type(Systeme::class)])]
        #[Assert\Valid]
        public readonly array $systemes,

        public readonly ?RefroidissementData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            systemes: Systeme::from_collection($entity->systemes()),
            data: RefroidissementData::from($entity),
        );
    }

    #[Assert\IsTrue]
    public function is_generateur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($this->generateurs as $generateur) {
                if ($generateur->id === $systeme->generateur_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    #[Assert\IsTrue]
    public function is_installation_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($this->installations as $installation) {
                if ($installation->id === $systeme->installation_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }
}
