<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLReader;
use App\Domain\Audit\Enum\{Typologie, PositionLogement};
use App\Domain\Common\ValueObject\Id;

final class XMLLogementReader extends XMLReader
{
    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return $this->findOne('//description')?->strval() ?? 'Logement reconstitué';
    }

    public function position(): PositionLogement
    {
        return ($id = $this->enum_position_etage_logement_id()) ?
            PositionLogement::from_enum_position_etage_logement_id($id) :
            PositionLogement::RDC;
    }

    public function typologie(): Typologie
    {
        if ($id = $this->enum_typologie_logement_id()) {
            return Typologie::from_enum_typologie_logement_id($id);
        }
        $surface = $this->surface_habitable();

        return match (true) {
            $surface < 30 => Typologie::T1,
            $surface < 40 => Typologie::T2,
            $surface < 60 => Typologie::T3,
            $surface < 80 => Typologie::T4,
            $surface < 100 => Typologie::T5,
            $surface < 120 => Typologie::T6,
            $surface >= 120 => Typologie::T7,
        };
    }

    public function surface_habitable(): float
    {
        return $this->findOneOrError('//surface_habitable_logement')->floatval();
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->audit()->findOneOrError('//caracteristique_generale//hsp')->floatval();
    }

    public function enum_position_etage_logement_id(): ?int
    {
        return $this->findOne('//enum_position_etage_logement_id')?->intval();
    }

    public function enum_typologie_logement_id(): ?int
    {
        return $this->findOne('//enum_typologie_logement_id')?->intval();
    }
}
