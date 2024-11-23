<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Chauffage\Enum\LabelGenerateur;
use App\Domain\Chauffage\Enum\TypeChaudiere;

class Signaletique
{
    public function __construct(
        public readonly ?TypeChaudiere $type_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?bool $presence_regulation_combustion = null,
        public readonly ?bool $presence_ventouse = null,
        public readonly ?int $priorite_cascade = null,
        public readonly ?float $pn = null,
        public readonly ?float $rpn = null,
        public readonly ?float $rpint = null,
        public readonly ?float $qp0 = null,
        public readonly ?float $pveilleuse = null,
        public readonly ?float $scop = null,
        public readonly ?float $tfonc30 = null,
        public readonly ?float $tfonc100 = null,
    ) {}

    public function controle(): void
    {
        Assert::positif($this->pn);
        Assert::positif($this->rpn);
        Assert::positif($this->rpint);
        Assert::positif($this->qp0);
        Assert::positif($this->pveilleuse);
        Assert::positif($this->priorite_cascade);
        Assert::positif($this->tfonc30);
        Assert::positif($this->tfonc100);
        Assert::positif($this->scop);
    }
}
