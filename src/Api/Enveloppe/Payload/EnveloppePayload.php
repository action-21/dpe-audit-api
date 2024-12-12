<?php

namespace App\Api\Enveloppe\Payload;

use App\Api\Baie\Payload\BaiePayload;
use App\Api\Lnc\Payload\LncPayload;
use App\Api\Mur\Payload\MurPayload;
use App\Api\PlancherBas\Payload\PlancherBasPayload;
use App\Api\PlancherHaut\Payload\PlancherHautPayload;
use App\Api\PontThermique\Payload\PontThermiquePayload;
use App\Api\Porte\Payload\PortePayload;
use App\Domain\Enveloppe\Enum\Exposition;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property LncPayload[] $locaux_non_chauffes
 * @property MurPayload[] $murs
 * @property PlancherBasPayload[] $planchers_bas
 * @property PlancherHautPayload[] $planchers_hauts
 * @property BaiePayload[] $baies
 * @property PortePayload[] $portes
 * @property PontThermiquePayload[] $ponts_thermiques
 * @property PlancherIntermediairePayload[] $planchers_intermediaires
 * @property RefendPayload[] $refends
 */
final class EnveloppePayload
{
    public function __construct(
        public Exposition $exposition,

        #[Assert\Positive]
        public ?float $q4pa_conv,

        /** @var LncPayload[] */
        #[Assert\All([new Assert\Type(LncPayload::class)])]
        #[Assert\Valid]
        public array $locaux_non_chauffes = [],

        /** @var MurPayload[] */
        #[Assert\All([new Assert\Type(MurPayload::class)])]
        #[Assert\Valid]
        public array $murs = [],

        /** @var PlancherBasPayload[] */
        #[Assert\All([new Assert\Type(PlancherBasPayload::class)])]
        #[Assert\Valid]
        public array $planchers_bas = [],

        /** @var PlancherHautPayload[] */
        #[Assert\All([new Assert\Type(PlancherHautPayload::class)])]
        #[Assert\Valid]
        public array $planchers_hauts = [],

        /** @var BaiePayload[] */
        #[Assert\All([new Assert\Type(BaiePayload::class)])]
        #[Assert\Valid]
        public array $baies = [],

        /** @var PortePayload[] */
        #[Assert\All([new Assert\Type(PortePayload::class)])]
        #[Assert\Valid]
        public array $portes = [],

        /** @var PontThermiquePayload[] */
        #[Assert\All([new Assert\Type(PontThermiquePayload::class)])]
        #[Assert\Valid]
        public array $ponts_thermiques = [],

        /** @var PlancherIntermediairePayload[] */
        #[Assert\All([new Assert\Type(PlancherIntermediairePayload::class)])]
        #[Assert\Valid]
        public array $planchers_intermediaires = [],

        /** @var RefendPayload[] */
        #[Assert\All([new Assert\Type(RefendPayload::class)])]
        #[Assert\Valid]
        public array $refends = [],
    ) {}

    #[Assert\IsTrue]
    public function isMursValid(): bool
    {
        foreach ($this->murs as $item) {
            if ($item->position->local_non_chauffe_id) {
                return $this->lncExists($item->position->local_non_chauffe_id);
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function isPlanchersBasValid(): bool
    {
        foreach ($this->planchers_bas as $item) {
            if ($item->position->local_non_chauffe_id) {
                return $this->lncExists($item->position->local_non_chauffe_id);
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function isPlanchersHautsValid(): bool
    {
        foreach ($this->planchers_hauts as $item) {
            if ($item->position->local_non_chauffe_id) {
                return $this->lncExists($item->position->local_non_chauffe_id);
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function isBaiesValid(): bool
    {
        foreach ($this->baies as $item) {
            if ($item->position->local_non_chauffe_id) {
                return $this->lncExists($item->position->local_non_chauffe_id);
            }
            if ($item->position->paroi_id) {
                return $this->paroiExists($item->position->paroi_id);
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function isPortesValid(): bool
    {
        foreach ($this->portes as $item) {
            if ($item->position->local_non_chauffe_id) {
                return $this->lncExists($item->position->local_non_chauffe_id);
            }
            if ($item->position->paroi_id) {
                return $this->paroiExists($item->position->paroi_id);
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function isPontsThermiquesValid(): bool
    {
        foreach ($this->ponts_thermiques as $item) {
            if (false === $this->paroiExists($item->liaison->mur_id)) {
                return false;
            }
            if ($item->liaison->plancher_id && false === $this->paroiExists($item->liaison->plancher_id)) {
                return false;
            }
            if ($item->liaison->ouverture_id && false === $this->ouvertureExists($item->liaison->ouverture_id)) {
                return false;
            }
        }
        return true;
    }

    private function lncExists(string $id): bool
    {
        $collection = \array_filter($this->locaux_non_chauffes, fn(LncPayload $item): bool => $item->id === $id);
        return \count($collection) > 0;
    }

    private function paroiExists(string $id): bool
    {
        $collection = \array_filter(
            [...$this->murs, ...$this->planchers_bas, ...$this->planchers_hauts],
            fn(MurPayload|PlancherBasPayload|PlancherHautPayload $item): bool => $item->id === $id
        );
        return \count($collection) > 0;
    }

    private function ouvertureExists(string $id): bool
    {
        $collection = \array_filter(
            [...$this->baies, ...$this->portes],
            fn(BaiePayload|PortePayload $item): bool => $item->id === $id
        );
        return \count($collection) > 0;
    }
}
