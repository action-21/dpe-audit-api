<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, TypeVitrage};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Engine\Performance\Rule;

final class DeperditionBaieDoubleFenetre extends Rule
{
    public final const EPAISSEUR_LAME_AIR_DEFAUT = 6;

    private Baie $baie;
    private DoubleFenetre $double_fenetre;

    public function __construct(
        private readonly BaieTableValeurRepository $table_repository
    ) {}

    public function materiau(): Materiau
    {
        return $this->double_fenetre->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        if ($this->double_fenetre->vitrage()?->type_vitrage) {
            return $this->double_fenetre->vitrage()->type_vitrage;
        }
        if (null !== $this->double_fenetre->type_baie()->is_paroi_vitree()) {
            return TypeVitrage::SIMPLE_VITRAGE;
        }
        return null;
    }

    public function epaisseur_lame_air(): ?float
    {
        if (null !== $this->double_fenetre->vitrage()?->epaisseur_lame) {
            return $this->double_fenetre->vitrage()->epaisseur_lame;
        }
        if ($this->double_fenetre->vitrage()->type_vitrage === TypeVitrage::SIMPLE_VITRAGE) {
            if (null !== $this->double_fenetre->vitrage()->survitrage) {
                return $this->double_fenetre->vitrage()->survitrage->epaisseur_lame
                    ?? self::EPAISSEUR_LAME_AIR_DEFAUT;
            }
        }
        return self::EPAISSEUR_LAME_AIR_DEFAUT;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        if (null !== $this->double_fenetre->vitrage()?->nature_gaz_lame) {
            return $this->double_fenetre->vitrage()->nature_gaz_lame;
        }
        if ($this->type_vitrage()?->is_vitrage_complexe()) {
            return NatureGazLame::AIR;
        }
        return null;
    }

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug(): float
    {
        return $this->get('ug', function () {
            if ($this->double_fenetre->performance()->ug) {
                return $this->double_fenetre->performance()->ug;
            }
            if (null === $value = $this->table_repository->ug(
                type_baie: $this->double_fenetre->type_baie(),
                type_vitrage: $this->type_vitrage(),
                nature_gaz_lame: $this->nature_gaz_lame(),
                inclinaison_vitrage: $this->baie->position()->inclinaison,
                epaisseur_lame_air: $this->epaisseur_lame_air(),
            )) {
                throw new \DomainException('Valeur forfaitaire ug non trouvée');
            }
            return $value;
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie exprimé en W/m².K
     */
    public function uw(): float
    {
        return $this->get('uw', function () {
            if ($this->double_fenetre->performance()->uw) {
                return $this->double_fenetre->performance()->uw;
            }
            if (null === $uw = $this->table_repository->uw(
                ug: $this->ug(),
                type_baie: $this->double_fenetre->type_baie(),
                presence_soubassement: $this->double_fenetre->presence_soubassement(),
                materiau: $this->materiau(),
                presence_rupteur_pont_thermique: $this->double_fenetre->menuiserie()?->presence_rupteur_pont_thermique,
            )) {
                throw new \DomainException('Valeur forfaitaire uw non trouvée');
            }
            return $uw;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            if (null === $baie->double_fenetre()) {
                continue;
            }
            $this->baie = $baie;
            $this->double_fenetre = $baie->double_fenetre();
            $this->clear();

            $baie->double_fenetre()->calcule($baie->double_fenetre()->data()->with(
                ug: $this->ug(),
                uw: $this->uw(),
            ));
        }
    }
}
