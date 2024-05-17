<?php

namespace App\Domain\MasqueProche;

use App\Domain\Baie\Baie;
use App\Domain\Common\ValueObject\Id;
use App\Domain\MasqueProche\Enum\TypeMasqueProche;
use App\Domain\MasqueProche\ValueObject\Avancee;

/**
 * Obstacle d'environnement proche
 */
final class MasqueProche
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueProche $type_masque_proche,
        private ?Avancee $avancee = null,
    ) {
    }

    public static function create_fond_balcon_ou_fond_flanc_loggias(
        Baie $baie,
        string $description,
        Avancee $avancee,
    ): self {
        return new self(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque_proche: TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            avancee: $avancee,
        );
    }

    public static function create_balcon_ou_auvent(
        Baie $baie,
        string $description,
        Avancee $avancee,
    ): self {
        return new self(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque_proche: TypeMasqueProche::BALCON_OU_AUVENT,
            avancee: $avancee,
        );
    }

    public static function create_paroi_laterale(
        Baie $baie,
        string $description,
        bool $obstacle_au_sud,
    ): self {
        return new MasqueProche(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque_proche: $obstacle_au_sud
                ? TypeMasqueProche::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD
                : TypeMasqueProche::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
        );
    }

    public function update(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function set_fond_balcon_ou_fond_flanc_loggias(Avancee $avancee): self
    {
        $this->avancee = $avancee;
        $this->type_masque_proche = TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS;
        return $this;
    }

    public function set_balcon_ou_auvent(Avancee $avancee): self
    {
        $this->avancee = $avancee;
        $this->type_masque_proche = TypeMasqueProche::BALCON_OU_AUVENT;
        return $this;
    }

    public function set_paroi_laterale(bool $obstacle_au_sud): self
    {
        $this->avancee = null;
        $this->type_masque_proche = $obstacle_au_sud
            ? TypeMasqueProche::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD
            : TypeMasqueProche::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD;

        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function baie(): Baie
    {
        return $this->baie;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function avancee(): ?Avancee
    {
        return $this->avancee;
    }

    public function type_masque_proche(): TypeMasqueProche
    {
        return $this->type_masque_proche;
    }
}
