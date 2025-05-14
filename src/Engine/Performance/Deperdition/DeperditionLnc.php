<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Service\LncTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditiveEnveloppe;

final class DeperditionLnc extends Rule
{
    private Lnc $lnc;

    public function __construct(
        private readonly LncTableValeurRepository $table_repository,
    ) {}

    /**
     * Somme des surfaces des parois donannt sur l'extérieur
     */
    public function aue(?EtatIsolation $isolation = null): float
    {
        $aue = 0;

        foreach ($this->lnc->baies() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aue += $paroi->position()->surface;
        }
        foreach ($this->lnc->parois_opaques() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aue += $paroi->position()->surface;
        }
        return $aue;
    }

    /**
     * Somme des surfaces des parois donnant sur l'espace chauffé
     * 
     * TODO: Vérifier l'état d'isolation des planchers bas dans la méthode conventionnelle
     */
    public function aiu(?EtatIsolation $isolation = null): float
    {
        $aiu = 0;

        foreach ($this->lnc->baies() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->position()->surface;
        }
        foreach ($this->lnc->parois_opaques() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->position()->surface;
        }
        foreach ($this->lnc->enveloppe()->baies() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->surface_reference();
        }
        foreach ($this->lnc->enveloppe()->portes() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->surface_reference();
        }
        foreach ($this->lnc->enveloppe()->murs() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->surface_reference();
        }
        foreach ($this->lnc->enveloppe()->planchers_bas() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->surface_reference();
        }
        foreach ($this->lnc->enveloppe()->planchers_hauts() as $paroi) {
            if ($isolation && $paroi->data()->isolation !== $isolation) {
                continue;
            }
            $aiu += $paroi->surface_reference();
        }

        return $aiu;
    }

    /**
     * Etat d'isolation majoritaire des parois du local non chauffé donnant sur l'extérieur
     */
    public function isolation_aue(): EtatIsolation
    {
        $aue = $this->aue();
        $aue_isole = $this->aue(EtatIsolation::ISOLE);
        return $aue_isole > $aue / 2 ? EtatIsolation::ISOLE : EtatIsolation::NON_ISOLE;
    }

    /**
     * Etat d'isolation majoritaire des parois du local non chauffé donnant sur l'espace chauffé
     */
    public function isolation_aiu(): EtatIsolation
    {
        $aiu = $this->aiu();
        $aiu_isole = $this->aiu(EtatIsolation::ISOLE);
        return $aiu_isole > $aiu / 2 ? EtatIsolation::ISOLE : EtatIsolation::NON_ISOLE;
    }

    /**
     * Coefficient surfacique équivalent exprimé en W/(m2.K)
     */
    public function uvue(): float
    {
        return $this->get('uvue', function () {
            if (null === $value = $this->table_repository->uvue(
                type_lnc: $this->lnc->type(),
            )) {
                throw new \DomainException("Valeur forfaitaire Uvue non trouvée");
            }
            return $value;
        });
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): float
    {
        return $this->get('b', function () {
            if (null === $value = $this->table_repository->b(
                uvue: $this->uvue(),
                isolation_aiu: $this->isolation_aiu(),
                isolation_aue: $this->isolation_aue(),
                aiu: $this->aiu(),
                aue: $this->aue(),
            )) {
                throw new \DomainException("Valeur forfaitaire b non trouvée");
            }
            return $value;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->locaux_non_chauffes() as $lnc) {
            $this->lnc = $lnc;
            $this->clear();

            $lnc->calcule($lnc->data()->with(
                aue: $this->aue(),
                aiu: $this->aiu(),
                isolation_aiu: $this->isolation_aiu(),
                isolation_aue: $this->isolation_aue(),
                uvue: $this->uvue(),
                b: $this->b(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            DeperditionBaieLnc::class,
            DeperditionParoiOpaqueLnc::class,
            SurfaceDeperditiveEnveloppe::class,
        ];
    }
}
