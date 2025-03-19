<?php

namespace App\Api\PontThermique\Payload;

use App\Domain\Common\ValueObject\Id;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\ValueObject\Liaison;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

final class LiaisonPayload implements GroupSequenceProviderInterface
{
    public function __construct(
        public TypeLiaison $type,
        #[Assert\Uuid]
        public string $mur_id,
        #[Assert\NotNull(groups: ['PLANCHER_BAS_MUR', 'PLANCHER_HAUT_MUR'])]
        #[Assert\Uuid]
        public ?string $plancher_id,
        #[Assert\NotNull(groups: ['MENUISERIE_MUR'])]
        #[Assert\Uuid]
        public ?string $ouverture_id,
        #[Assert\NotNull(groups: ['PLANCHER_INTERMEDIAIRE_MUR', 'REFEND_MUR'])]
        public ?bool $pont_thermique_partiel,
    ) {}

    public function to(): Liaison
    {
        return match ($this->type) {
            TypeLiaison::PLANCHER_BAS_MUR => Liaison::create_liaison_plancher_bas_mur(
                mur_id: Id::from($this->mur_id),
                plancher_id: Id::from($this->plancher_id),
            ),
            TypeLiaison::PLANCHER_HAUT_MUR => Liaison::create_liaison_plancher_haut_mur(
                mur_id: Id::from($this->mur_id),
                plancher_id: Id::from($this->plancher_id),
            ),
            TypeLiaison::MENUISERIE_MUR => Liaison::create_liaison_menuiserie_mur(
                mur_id: Id::from($this->mur_id),
                ouverture_id: Id::from($this->ouverture_id),
            ),
            TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR => Liaison::create_liaison_plancher_intermediaire_mur(
                mur_id: Id::from($this->mur_id),
                pont_thermique_partiel: $this->pont_thermique_partiel,
            ),
            TypeLiaison::REFEND_MUR => Liaison::create_liaison_refend_mur(
                mur_id: Id::from($this->mur_id),
                pont_thermique_partiel: $this->pont_thermique_partiel,
            )
        };
    }

    public function getGroupSequence(): array|GroupSequence
    {
        return ['LiaisonPayload', $this->type->value];
    }
}
