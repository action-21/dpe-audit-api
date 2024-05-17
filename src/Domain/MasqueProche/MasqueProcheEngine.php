<?php

namespace App\Domain\MasqueProche;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\MasqueProche\Enum\TypeMasqueProche;
use App\Domain\MasqueProche\Table\{Fe1, Fe1Repository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §6.2.2.1 - Masques proches
 */
final class MasqueProcheEngine
{
    private MasqueProche $input;
    private SimulationEngine $context;

    private ?Fe1 $table_fe1;

    public function __construct(private Fe1Repository $table_fe1_repository)
    {
    }

    /**
     * Facteur d'ensoleillement du fait du masque proche
     */
    public function fe1(): float
    {
        return $this->table_fe1()->fe1;
    }

    /**
     * Valeur de la table masque proche . fe1
     */
    public function table_fe1(): Fe1
    {
        if (null === $this->table_fe1) {
            throw new EngineTableError('masque_proche . fe1');
        }
        return $this->table_fe1;
    }

    public function fetch(): void
    {
        $this->table_fe1 = $this->table_fe1_repository->find_by(
            type_masque_proche: $this->type_masque_proche(),
            orientation: $this->orientation(),
            avancee: $this->avancee(),
        );
    }

    // * Données d'entrée

    public function type_masque_proche(): TypeMasqueProche
    {
        return $this->input->type_masque_proche();
    }

    public function orientation(): ?Orientation
    {
        return $this->input->baie()->orientation()?->enum();
    }

    public function avancee(): ?float
    {
        return $this->input->avancee()?->valeur();
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function input(): MasqueProche
    {
        return $this->input;
    }

    public function __invoke(MasqueProche $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
