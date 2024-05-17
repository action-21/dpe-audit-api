<?php

namespace App\Domain\MasqueLointain;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\MasqueLointain\Enum\{SecteurOrientation, TypeMasqueLointain};
use App\Domain\MasqueLointain\Table\{Fe2Repository, Fe2, Omb, OmbRepository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §6.2.2.2 Masques lointains
 */
final class MasqueLointainEngine
{
    private MasqueLointain $input;
    private SimulationEngine $context;

    private ?Fe2 $table_fe2;
    private ?Omb $table_omb;

    public function __construct(
        private Fe2Repository $table_fe2_repository,
        private OmbRepository $table_omb_repository,
    ) {
    }

    /**
     * Facteur d'ensoleillement dû au masque lointain
     */
    public function fe2(): null|float
    {
        if (false === $this->calcul_fe2()) {
            return null;
        }
        if (null === $this->table_fe2()) {
            throw new EngineTableError('masque_lointain . fe2');
        }
        return $this->table_fe2()->fe2;
    }

    /**
     * Ombrage dû au masque lointain non homogène
     */
    public function omb(): null|float
    {
        if (false === $this->calcul_omb()) {
            return null;
        }
        if (null === $this->table_omb()) {
            throw new EngineTableError('masque_lointain . omb');
        }
        return $this->table_omb()->omb;
    }

    public function calcul_fe2(): bool
    {
        return $this->type_masque() === TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE;
    }

    public function calcul_omb(): bool
    {
        return $this->type_masque() === TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE;
    }

    /**
     * Valeur de la table masque lointain . fe2
     */
    public function table_fe2(): ?Fe2
    {
        return $this->table_fe2;
    }

    /**
     * Valeur de la table masque lointain . omb
     */
    public function table_omb(): ?Omb
    {
        return $this->table_omb;
    }

    public function fetch(): void
    {
        $this->table_fe2 = $this->calcul_fe2() ? $this->table_fe2_repository->find_by(
            orientation: $this->orientation(),
            hauteur_alpha: $this->hauteur_alpha()
        ) : null;

        $this->table_omb = $this->calcul_omb() ? $this->table_omb_repository->find_by(
            secteur_orientation: $this->secteur_orientation(),
            orientation: $this->orientation(),
            hauteur_alpha: $this->hauteur_alpha()
        ) : null;
    }

    // * Données d'entrée

    public function type_masque(): TypeMasqueLointain
    {
        return $this->input->type_masque();
    }

    public function secteur_orientation(): ?SecteurOrientation
    {
        return $this->input->secteur_orientation();
    }

    public function orientation(): Orientation
    {
        return $this->input->orientation()->enum();
    }

    public function hauteur_alpha(): float
    {
        return $this->input->hauteur_alpha()->valeur();
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function input(): MasqueLointain
    {
        return $this->input;
    }

    public function __invoke(MasqueLointain $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
