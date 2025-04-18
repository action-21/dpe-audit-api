<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Id, Orientation};
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;

abstract class XMLMasqueLointainReader extends XMLReader
{
    abstract public function id(): Id;

    abstract public function description(): string;

    abstract public function type_masque(): TypeMasqueLointain;

    abstract public function orientation(): Orientation;

    abstract public function hauteur(): float;
}
