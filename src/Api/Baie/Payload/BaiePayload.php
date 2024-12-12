<?php

namespace App\Api\Baie\Payload;

use App\Api\Baie\Payload\Caracteristique;
use App\Api\Baie\Payload\DoubleFenetre;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property MasqueProchePayload[] $masques_proches
 * @property MasqueLointainPayload[] $masques_lointains
 */
final class BaiePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload $position,
        #[Assert\Valid]
        public Caracteristique\FenetrePayload|Caracteristique\ParoiVitreePayload|Caracteristique\PorteFenetrePayload $caracteristique,
        #[Assert\Valid]
        public null|DoubleFenetre\FenetrePayload|DoubleFenetre\ParoiVitreePayload|DoubleFenetre\PorteFenetrePayload $double_fenetre,
        /** @var MasqueProchePayload[] */
        #[Assert\All([new Assert\Type(MasqueProchePayload::class)])]
        #[Assert\Valid]
        public array $masques_proches,
        /** @var MasqueLointainPayload[] */
        #[Assert\All([new Assert\Type(MasqueLointainPayload::class)])]
        #[Assert\Valid]
        public array $masques_lointains,
    ) {}
}
