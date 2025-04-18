<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Position, Signaletique};
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class RadiateurGazFactory extends GenerateurFactory
{
    public function set_position(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: false,
            generateur_multi_batiment: false,
        );
        return $this;
    }

    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        Assert::nullOrInArray($signaletique->label, [LabelGenerateur::FLAMME_VERTE]);
        $this->signaletique = Signaletique::create(
            pn: $signaletique->pn,
            label: $signaletique->label,
            combustion: $signaletique->combustion ?? Combustion::create(),
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type === TypeGenerateur::RADIATEUR_GAZ && $energie->is_gaz();
    }
}
