<?php

namespace App\Application\Porte\Create;

use App\Domain\Porte\Enum\{Mitoyennete, NatureMenuiserie, TypePorte, TypePose};
use App\Domain\Porte\ValueObject\{Caracteristique, LargeurDormant, Surface, Uporte};
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $enveloppe_id,
        #[Assert\NotBlank]
        public readonly ?string $paroi_opaque_id,
        #[Assert\NotBlank]
        public readonly ?string $local_non_chauffe_id,
        #[Assert\NotBlank]
        public readonly string $description,
        #[Assert\NotBlank]
        public readonly Mitoyennete $mitoyennete,
        #[Assert\NotBlank]
        public readonly TypePose $type_pose,
        #[Assert\NotBlank]
        public readonly NatureMenuiserie $nature_menuiserie,
        #[Assert\NotBlank]
        public readonly TypePorte $type_porte,
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly float $surface,
        #[Assert\NotNull]
        public readonly bool $presence_joint,
        public readonly ?bool $presence_retour_isolation = null,
        #[Assert\Positive]
        public readonly ?float $largeur_dormant = null,
        #[Assert\Positive]
        public readonly ?float $uporte = null,
    ) {
    }

    public function caracteristique(): Caracteristique
    {
        return Caracteristique::create(
            surface: Surface::from($this->surface),
            type_pose: $this->type_pose,
            nature_menuiserie: $this->nature_menuiserie,
            type_porte: $this->type_porte,
            presence_joint: $this->presence_joint,
            presence_retour_isolation: $this->presence_retour_isolation,
            largeur_dormant: $this->largeur_dormant ? LargeurDormant::from($this->largeur_dormant) : null,
            uporte: $this->uporte ? Uporte::from($this->uporte) : null,
        );
    }
}
