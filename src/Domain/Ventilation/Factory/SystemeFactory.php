<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\{Generateur, Installation, Systeme};
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};

final class SystemeFactory
{
    private Id $id;
    private Installation $installation;

    public function initialise(Id $id, Installation $installation): self
    {
        $this->id = $id;
        $this->installation = $installation;
        return $this;
    }

    private function build(
        TypeSysteme $type,
        ?Generateur $generateur = null,
        ?ModeExtraction $mode_extraction = null,
        ?ModeInsufflation $mode_insufflation = null,
    ): Systeme {
        $entity = new Systeme(
            id: $this->id,
            installation: $this->installation,
            type: $type,
            generateur: $generateur,
            mode_extraction: $mode_extraction,
            mode_insufflation: $mode_insufflation,
        );

        $entity->controle();
        return $entity;
    }

    public function build_ventilation_naturelle(
        ?ModeExtraction $mode_extraction,
        ?ModeInsufflation $mode_insufflation,
    ): Systeme {
        return $this->build(
            mode_extraction: $mode_extraction,
            mode_insufflation: $mode_insufflation,
            type: TypeSysteme::VENTILATION_NATURELLE,
        );
    }

    public function build_ventilation_centralisee(
        Generateur $generateur,
        ModeExtraction $mode_extraction,
        ModeInsufflation $mode_insufflation,
    ): Systeme {
        return $this->build(
            generateur: $generateur,
            mode_extraction: $mode_extraction,
            mode_insufflation: $mode_insufflation,
            type: TypeSysteme::from_type_generateur($generateur->type()),
        );
    }

    public function build_ventilation_divisee(Generateur $generateur): Systeme
    {
        return $this->build(
            generateur: $generateur,
            type: TypeSysteme::from_type_generateur($generateur->type()),
        );
    }
}
