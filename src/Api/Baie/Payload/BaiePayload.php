<?php

namespace App\Api\Baie\Payload;

use App\Api\Baie\Payload\Caracteristique;
use App\Api\Baie\Payload\DoubleFenetre;
use App\Api\Baie\Payload\MasqueProche;
use App\Api\Baie\Payload\Position\{PositionPayload, PositionWithLncPayload, PositionWithParoiPayload};
use Symfony\Component\Validator\Constraints as Assert;

final class BaiePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload|PositionWithLncPayload|PositionWithParoiPayload $position,
        #[Assert\Valid]
        public Caracteristique\FenetrePayload|Caracteristique\ParoiVitreePayload|Caracteristique\PorteFenetrePayload $caracteristique,
        #[Assert\Valid]
        public null|DoubleFenetre\FenetrePayload|DoubleFenetre\ParoiVitreePayload|DoubleFenetre\PorteFenetrePayload $double_fenetre,
        /** @var (MasqueProche\BalconAuvent|MasqueProche\ParoiLaterale)[] */
        #[Assert\All([new Assert\Type([MasqueProche\BalconAuvent::class, MasqueProche\ParoiLaterale::class])])]
        #[Assert\Valid]
        public array $masques_proches,
        /** @var MasqueLointainPayload[] */
        #[Assert\All([new Assert\Type(MasqueLointainPayload::class)])]
        #[Assert\Valid]
        public array $masques_lointains,
    ) {}
}
