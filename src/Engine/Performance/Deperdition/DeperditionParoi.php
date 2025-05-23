<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\Enveloppe\Service\ParoiTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;
use App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditiveEnveloppe;

/**
 * @template T
 * 
 * @property Paroi|T $paroi
 */
abstract class DeperditionParoi extends Rule
{
    protected readonly ParoiTableValeurRepository $table_paroi_repository;
    protected Audit $audit;
    protected Paroi $paroi;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    /**
     * @see \App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditiveParoi::sdep()
     */
    abstract public function sdep(): float;

    /**
     * @see \App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditiveParoi::isolation()
     */
    abstract public function isolation(): EtatIsolation;

    /**
     * Coefficient de transmission thermique exprimé en W/m².K
     */
    abstract public function u(): float;

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): float
    {
        if ($this->paroi->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL) {
            return 0;
        }
        if ($this->paroi->local_non_chauffe()?->data()->b) {
            return $this->paroi->local_non_chauffe()->data()->b;
        }
        if (null !== $value = $this->bver()) {
            return $value;
        }
        if (null === $value = $this->table_paroi_repository->b(
            mitoyennete: $this->paroi->mitoyennete(),
        )) {
            throw new \DomainException('Valeur forfaitaire b non trouvée');
        }
        return $value;
    }

    /**
     * Coefficient de réduction des déperditions thermiques de l'espace tampon solarisé
     */
    public function bver(): ?float
    {
        if (null === $this->paroi->local_non_chauffe()) {
            return null;
        }
        if (false === $this->paroi->local_non_chauffe()->type()->is_ets()) {
            return null;
        }
        if (null === $value = $this->table_paroi_repository->bver(
            zone_climatique: $this->zone_climatique(),
            isolation_paroi: $this->isolation(),
            orientations_lnc: $this->paroi->local_non_chauffe()->orientations(),
        )) {
            throw new \DomainException('Valeur forfaitaire bver non trouvée');
        }
        return $value;
    }

    /**
     * Déperditions thermiques exprimées en W/K
     */
    public function dp(): float
    {
        if ($this->paroi->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL) {
            return 0;
        }
        $sdep = $this->sdep();
        $b = $this->b();
        $u = $this->u();

        return $u * $b * $sdep;
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            DeperditionLnc::class,
            SurfaceDeperditiveEnveloppe::class,
        ];
    }
}
