<?php

namespace App\Domain\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\Entity\{Lnc, LncCollection};
use App\Domain\Enveloppe\Entity\{Baie, BaieCollection};
use App\Domain\Enveloppe\Entity\{Mur, MurCollection};
use App\Domain\Enveloppe\Entity\{Niveau, NiveauCollection};
use App\Domain\Enveloppe\Entity\{Paroi, ParoiCollection};
use App\Domain\Enveloppe\Entity\{PlancherBas, PlancherBasCollection};
use App\Domain\Enveloppe\Entity\{PlancherHaut, PlancherHautCollection};
use App\Domain\Enveloppe\Entity\{Porte, PorteCollection};
use App\Domain\Enveloppe\Entity\{PontThermique, PontThermiqueCollection};
use App\Domain\Enveloppe\Enum\TypeParoi;
use Webmozart\Assert\Assert;

final class Enveloppe
{
    public function __construct(
        private readonly Id $id,
        private Exposition $exposition,
        private ?float $q4pa_conv,
        private bool $presence_brasseurs_air,
        private LncCollection $locaux_non_chauffes,
        private BaieCollection $baies,
        private MurCollection $murs,
        private PlancherBasCollection $planchers_bas,
        private PlancherHautCollection $planchers_hauts,
        private PorteCollection $portes,
        private PontThermiqueCollection $ponts_thermiques,
        private NiveauCollection $niveaux,
        private EnveloppeData $data,
    ) {}

    public static function create(Exposition $exposition, ?float $q4pa_conv, bool $presence_brasseurs_air): self
    {
        Assert::nullOrGreaterThan($q4pa_conv, 0);

        return new self(
            id: Id::create(),
            exposition: $exposition,
            q4pa_conv: $q4pa_conv,
            presence_brasseurs_air: $presence_brasseurs_air,
            niveaux: new NiveauCollection,
            locaux_non_chauffes: new LncCollection,
            baies: new BaieCollection,
            murs: new MurCollection,
            planchers_bas: new PlancherBasCollection,
            planchers_hauts: new PlancherHautCollection,
            portes: new PorteCollection,
            ponts_thermiques: new PontThermiqueCollection,
            data: EnveloppeData::create(),
        );
    }

    public function reinitialise(): void
    {
        $this->data = EnveloppeData::create();
        $this->niveaux->reinitialise();
        $this->locaux_non_chauffes->reinitialise();
        $this->baies->reinitialise();
        $this->murs->reinitialise();
        $this->planchers_bas->reinitialise();
        $this->planchers_hauts->reinitialise();
        $this->portes->reinitialise();
    }

    public function calcule(EnveloppeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function exposition(): Exposition
    {
        return $this->exposition;
    }

    public function q4pa_conv(): ?float
    {
        return $this->q4pa_conv;
    }

    public function presence_brasseurs_air(): bool
    {
        return $this->presence_brasseurs_air;
    }

    /**
     * @return NiveauCollection|Niveau[]
     */
    public function niveaux(): NiveauCollection
    {
        return $this->niveaux;
    }

    public function add_niveau(Niveau $entity): self
    {
        $this->niveaux->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return LncCollection|Lnc[]
     */
    public function locaux_non_chauffes(): LncCollection
    {
        return $this->locaux_non_chauffes;
    }

    public function add_local_non_chauffe(Lnc $entity): self
    {
        $this->locaux_non_chauffes->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return ParoiCollection|Paroi[]
     */
    public function parois(TypeParoi $type_paroi): ParoiCollection
    {
        return match ($type_paroi) {
            TypeParoi::MUR => $this->murs,
            TypeParoi::PLANCHER_BAS => $this->planchers_bas,
            TypeParoi::PLANCHER_HAUT => $this->planchers_hauts,
            TypeParoi::PORTE => $this->portes,
            TypeParoi::BAIE => $this->baies,
        };
    }

    public function paroi(Id $id): ?Paroi
    {
        foreach (TypeParoi::cases() as $type_paroi) {
            if ($paroi = $this->parois($type_paroi)->find($id)) {
                return $paroi;
            }
        }
        return null;
    }

    /**
     * @return BaieCollection|Baie[]
     */
    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        $this->baies->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return MurCollection|Mur[]
     */
    public function murs(): MurCollection
    {
        return $this->murs;
    }

    public function add_mur(Mur $entity): self
    {
        $this->murs->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return PlancherBasCollection|PlancherBas[]
     */
    public function planchers_bas(): PlancherBasCollection
    {
        return $this->planchers_bas;
    }

    public function add_plancher_bas(PlancherBas $entity): self
    {
        $this->planchers_bas->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return PlancherHautCollection|PlancherHaut[]
     */
    public function planchers_hauts(): PlancherHautCollection
    {
        return $this->planchers_hauts;
    }

    public function add_plancher_haut(PlancherHaut $entity): self
    {
        $this->planchers_hauts->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return PorteCollection|Porte[]
     */
    public function portes(): PorteCollection
    {
        return $this->portes;
    }

    public function add_porte(Porte $entity): self
    {
        $this->portes->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return PontThermiqueCollection|PontThermique[]
     */
    public function ponts_thermiques(): PontThermiqueCollection
    {
        return $this->ponts_thermiques;
    }

    public function add_pont_thermique(PontThermique $entity): self
    {
        $this->ponts_thermiques->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): EnveloppeData
    {
        return $this->data;
    }
}
