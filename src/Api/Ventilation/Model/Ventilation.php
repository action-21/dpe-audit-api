<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Ventilation\Ventilation as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property Generateur[] $generateurs
 * @property Installation[] $installations
 * @property Systeme[] $systemes
 */
final class Ventilation
{
    public function __construct(
        public string $id,

        /** @var Generateur[] */
        #[Assert\All([new Assert\Type(Generateur::class)])]
        #[Assert\Valid]
        public array $generateurs,

        /** @var Installation[] */
        #[Assert\All([new Assert\Type(Installation::class)])]
        #[Assert\Valid]
        public array $installations,

        /** @var Systeme[] */
        #[Assert\All([new Assert\Type(Systeme::class)])]
        #[Assert\Valid]
        public array $systemes,

        public ?VentilationData $data,
    ) {}

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

    #[Assert\IsTrue]
    public function is_generateur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            if ($systeme->generateur_id === null) {
                continue;
            }
            foreach ($this->generateurs as $generateur) {
                if ($generateur->id === $systeme->generateur_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            systemes: Systeme::from_collection($entity->systemes()),
            data: VentilationData::from($entity),
        );
    }
}
