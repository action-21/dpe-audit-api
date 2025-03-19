<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Enum\{Orientation, ZoneClimatique};
use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Lnc\Data\{BRepository, BverCollection, BVerRepository, C1Repository, TRepository, UvueRepository};
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\Performance;

/**
 * @uses \App\Domain\Lnc\Service\MoteurSurfaceDeperditive
 */
final class MoteurPerformance extends MoteurCalcul
{
    public function __construct(
        private UvueRepository $uvue_repository,
        private BRepository $b_repository,
        private BVerRepository $bver_repository,
        private C1Repository $c1_repository,
        private TRepository $t_repository,
    ) {}

    public function uvue(TypeLnc $type_lnc): float
    {
        if (null === $valeur = $this->uvue_repository->find_by(type_lnc: $type_lnc))
            throw new \DomainException("Valeur forfaitaire Uvue non trouvée");

        return $valeur->uvue;
    }

    public function b(float $uvue, float $aiu, float $aue, bool $isolation_aiu, bool $isolation_aue): float
    {
        if (null === $valeur = $this->b_repository->find_by(
            uvue: $uvue,
            aiu: $aiu,
            aue: $aue,
            isolation_aiu: $isolation_aiu,
            isolation_aue: $isolation_aue,
        )) throw new \DomainException("Valeur forfaitaire b non trouvée");

        return $valeur->b;
    }

    /**
     * @param Orientation[] $orientations - Orientations principales des baies de l'espace tampon solarisé
     */
    public function bver(ZoneClimatique $zone_climatique, array $orientations): BverCollection
    {
        $collection = $this->bver_repository
            ->search_by(zone_climatique: $zone_climatique)
            ->filter_by_orientations(orientations: $orientations);

        if ($collection->count() === 0)
            throw throw new \DomainException("Valeur forfaitaire bver non trouvée");

        return $collection;
    }

    public function __invoke(Lnc $entity): Performance
    {
        if ($entity->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
            return Performance::create_ets(bvers: $this->bver(
                zone_climatique: $entity->zone_climatique(),
                orientations: $entity->baies()->orientations(),
            ));
        }
        return Performance::create(
            uvue: ($uvue = $this->uvue(type_lnc: $entity->type())),
            b: $this->b(
                uvue: $uvue,
                aiu: $entity->surface_deperditive()->aiu,
                aue: $entity->surface_deperditive()->aue,
                isolation_aiu: $entity->surface_deperditive()->isolation_aiu->boolval(),
                isolation_aue: $entity->surface_deperditive()->isolation_aue->boolval(),
            ),
        );
    }
}
