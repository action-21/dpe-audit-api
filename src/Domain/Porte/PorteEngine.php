<?php

namespace App\Domain\Porte;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Porte\Enum\{NatureMenuiserie, QualiteComposant, TypePorte};
use App\Domain\Porte\Table\{B, BRepository, Uporte, UporteRepository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.3.4 - Coefficients U des portes
 * 
 * @see \App\Domain\Lnc\LncEngineCollection
 */
final class PorteEngine
{
    private Porte $input;
    private SimulationEngine $context;

    private ?B $table_b;
    private ?Uporte $table_uporte;

    public function __construct(
        private BRepository $table_b_repository,
        private UporteRepository $table_uporte_repository,
    ) {
    }

    /**
     * DP,porte - Déperditions thermiques (W/K)
     */
    public function dp(): float
    {
        return $this->u() * $this->sdep() * $this->b();
    }

    /**
     * sdep,porte - Surface déperditive (m²)
     */
    public function sdep(): float
    {
        return $this->surface_reference();
    }

    /**
     * u,porte - Coefficient de transmission thermique (W/(m².K))
     */
    public function u(): float
    {
        if ($this->uporte_saisi()) {
            return $this->uporte_saisi();
        }
        if (null === $this->table_uporte()) {
            throw new EngineTableError('porte . uporte');
        }
        return $this->table_uporte()->valeur();
    }

    /**
     * b,paroi - Coefficient de réduction thermique
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function b(): float
    {
        if (null === $this->local_non_chauffe_id()) {
            if (null === $this->table_b()) {
                throw new EngineTableError('porte . b');
            }
            return $this->table_b()->valeur();
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->b($this->local_non_chauffe_id())) {
            throw new EngineValeurError('porte . b');
        }
        return $value;
    }

    /**
     * Indicateur de performance de l'élément
     */
    public function qualite_isolation(): QualiteComposant
    {
        return QualiteComposant::from_uporte($this->u());
    }

    /**
     * Retoure la valeur de la table paroi . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    /**
     * Retoure la valeur de la table porte . uporte
     */
    public function table_uporte(): ?Uporte
    {
        return $this->table_uporte;
    }

    public function fetch(): void
    {
        $this->table_b = $this->table_b_repository->find_by(
            mitoyennete: $this->mitoyennete()
        );
        $this->table_uporte = $this->table_uporte_repository->find_by(
            type_porte: $this->type_porte(),
            nature_menuiserie: $this->nature_menuiserie(),
        );
    }

    // * Données d'entrée

    public function mitoyennete(): Enum
    {
        return $this->input->mitoyennete();
    }

    public function nature_menuiserie(): NatureMenuiserie
    {
        return $this->input->caracteristique()->nature_menuiserie;
    }

    public function type_porte(): TypePorte
    {
        return $this->input->caracteristique()->type_porte;
    }

    public function surface_reference(): float
    {
        return $this->input->surface_deperditive();
    }

    public function uporte_saisi(): ?float
    {
        return $this->input->caracteristique()->uporte?->valeur();
    }

    public function local_non_chauffe_id(): ?Id
    {
        return $this->input->local_non_chauffe()?->id();
    }

    public function input(): Porte
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Porte $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
