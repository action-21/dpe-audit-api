<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Functions;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionBaie;
use App\Domain\Enveloppe\Entity\{Baie, Lnc};
use App\Domain\Enveloppe\Entity\Lnc\Baie as BaieLnc;
use App\Domain\Enveloppe\ValueObject\Lnc\{Ensoleillement, Ensoleillements};

final class EnsoleillementLnc extends EngineRule
{
    private Lnc $lnc;

    /**
     * Coefficient de réduction des déperditions de l'espace tampon solarisé
     */
    public function bver(): float
    {
        $baies = $this->lnc->enveloppe()->baies()->with_local_non_chauffe($this->lnc->id());
        return Functions::moyenne_ponderee(
            valeurs: $baies->map(fn(Baie $entity) => $entity->data()->b)->values(),
            coefficients: $baies->map(fn(Baie $entity) => $entity->surface_reference())->values(),
        );
    }

    /**
     * Facteur d'ensoleillement moyen
     */
    public function fe(Mois $mois): float
    {
        return Functions::moyenne_ponderee(
            valeurs: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->data()->ensoleillement($mois)->fe)
                ->values(),
            coefficients: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->position()->surface)
                ->values(),
        );
    }

    /**
     * Coefficient d'orientation et d'inclinaison moyen
     */
    public function c1(Mois $mois): float
    {
        return Functions::moyenne_ponderee(
            valeurs: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->data()->ensoleillement($mois)->c1)
                ->values(),
            coefficients: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->position()->surface)
                ->values(),
        );
    }

    /**
     * Coefficient de transparence moyen
     */
    public function t(Mois $mois): float
    {
        return Functions::moyenne_ponderee(
            valeurs: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->data()->ensoleillement($mois)->t)
                ->values(),
            coefficients: $this->lnc->baies()
                ->map(fn(BaieLnc $baie): float => $baie->position()->surface)
                ->values(),
        );
    }

    /**
     * Surface sud équivalente des apports totaux dans la véranda exprimée en m²
     */
    public function sst(Mois $mois): float
    {
        return $this->lnc->baies()->reduce(
            fn(float $sst, BaieLnc $baie): float => $sst + $baie->data()->ensoleillement($mois)->sst
        );
    }

    /**
     * Surface sud équivalente représentant l’impact des apports solaires associés au rayonnement
     * solaire traversant directement l’espace tampon pour arriver dans la partie habitable du
     * logement exprimée en m²
     */
    public function ssd(Mois $mois): float
    {
        $baies = $this->lnc->enveloppe()->baies()->with_local_non_chauffe($this->lnc->id());

        return $baies->reduce(
            fn(float $ssd, Baie $entity): float => $ssd + $entity->data()->ensoleillements->sse($mois),
        ) * $this->t($mois);
    }

    /**
     * Surface sud équivalente représentant l’impact des apports solaires associés au rayonnement
     * solaire entrant dans la partie habitable du logement après de multiples réflexions dans
     * l’espace tampon solarisé exprimée en m²
     */
    public function ssind(Mois $mois): float
    {
        return $this->sst($mois) - $this->ssd($mois);
    }

    /**
     * Surface sud équivalente des apports solaires dans la véranda exprimée en m²
     */
    public function sse(Mois $mois): float
    {
        return $this->ssd($mois) + $this->ssind($mois) * $this->bver();
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->locaux_non_chauffes() as $lnc) {
            if (false === $lnc->type()->is_ets()) {
                continue;
            }
            $this->lnc = $lnc;

            $ensoleillements = Mois::each(fn(Mois $mois) => Ensoleillement::create(
                mois: $mois,
                fe: $this->fe($mois),
                c1: $this->c1($mois),
                t: $this->t($mois),
                sst: $this->sst($mois),
                ssd: $this->ssd($mois),
                ssind: $this->ssind($mois),
                sse: $this->sse($mois),
            ));
            $lnc->calcule($lnc->data()->with(
                bver: $this->bver(),
                ensoleillements: Ensoleillements::create(...$ensoleillements),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            EnsoleillementBaieLnc::class,
            DeperditionBaie::class,
        ];
    }
}
