<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, TypeFermeture, TypeVitrage};
use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance, TypeDeperdition};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

/**
 * @extends DeperditionParoi<Baie>
 */
final class DeperditionBaie extends DeperditionParoi
{
    public final const EPAISSEUR_LAME_AIR_DEFAUT = 6;

    public function __construct(
        private readonly BaieTableValeurRepository $table_repository,
    ) {
        $this->table_paroi_repository = $table_repository;
    }

    public function materiau(): Materiau
    {
        return $this->paroi->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        if ($this->paroi->vitrage()?->type_vitrage) {
            return $this->paroi->vitrage()->type_vitrage;
        }
        if (null !== $this->paroi->type_baie()->is_paroi_vitree()) {
            return TypeVitrage::SIMPLE_VITRAGE;
        }
        return null;
    }

    public function epaisseur_lame_air(): ?float
    {
        if (null !== $this->paroi->vitrage()?->epaisseur_lame) {
            return $this->paroi->vitrage()->epaisseur_lame;
        }
        if ($this->paroi->vitrage()?->type_vitrage === TypeVitrage::SIMPLE_VITRAGE) {
            if (null !== $this->paroi->vitrage()->survitrage) {
                return $this->paroi->vitrage()->survitrage->epaisseur_lame ?? self::EPAISSEUR_LAME_AIR_DEFAUT;
            }
        }
        return self::EPAISSEUR_LAME_AIR_DEFAUT;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        if (null !== $this->paroi->vitrage()?->nature_gaz_lame) {
            return $this->paroi->vitrage()->nature_gaz_lame;
        }
        if ($this->type_vitrage()?->is_vitrage_complexe()) {
            return NatureGazLame::AIR;
        }
        return null;
    }

    /** @inheritdoc */
    public function sdep(): float
    {
        return $this->paroi->data()->sdep;
    }

    /** @inheritdoc */
    public function isolation(): EtatIsolation
    {
        return $this->paroi->data()->isolation;
    }

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug(): float
    {
        return $this->get('ug', function () {
            return $this->paroi->double_fenetre()
                ? min($this->ug1(), $this->paroi->double_fenetre()->data()->ug)
                : $this->ug1();
        });
    }

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug1(): float
    {
        return $this->get('ug1', function () {
            if ($this->paroi->performance()->ug) {
                return $this->paroi->performance()->ug;
            }
            if (null === $value = $this->table_repository->ug(
                type_baie: $this->paroi->type_baie(),
                type_vitrage: $this->type_vitrage(),
                nature_gaz_lame: $this->nature_gaz_lame(),
                inclinaison_vitrage: $this->paroi->position()->inclinaison,
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
            return $this->paroi->double_fenetre()
                ? 1 / (1 / $this->uw1() + 1 / $this->paroi->double_fenetre()->data()->uw + 0.07)
                : $this->uw1();
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie exprimé en W/m².K
     */
    public function uw1(): float
    {
        return $this->get('uw1', function () {
            if ($this->paroi->performance()->uw) {
                return $this->paroi->performance()->uw;
            }
            if (null === $uw = $this->table_repository->uw(
                ug: $this->ug1(),
                type_baie: $this->paroi->type_baie(),
                presence_soubassement: $this->paroi->presence_soubassement(),
                materiau: $this->materiau(),
                presence_rupteur_pont_thermique: $this->paroi->menuiserie()?->presence_rupteur_pont_thermique,
            )) {
                throw new \DomainException('Valeur forfaitaire uw non trouvée');
            }
            return $uw;
        });
    }

    /**
     * Résistance thermique additionnelle due aux fermetures exprimée en m².K/W
     */
    public function deltar(): float
    {
        return $this->get('deltar', function () {
            if ($this->paroi->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return 0;
            }
            if (null === $deltar = $this->table_repository->deltar(
                type_fermeture: $this->paroi->type_fermeture(),
            )) {
                throw new \DomainException('Valeur forfaitaire deltar non trouvée');
            }
            return $deltar;
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie avec fermetures exprimé en W/m².K
     */
    public function ujn(): float
    {
        return $this->get('ujn', function () {
            if ($this->paroi->performance()->ujn) {
                return $this->paroi->performance()->ujn;
            }
            if ($this->paroi->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return $this->uw();
            }
            if (null === $ujn = $this->table_repository->ujn(
                deltar: $this->deltar(),
                uw: $this->uw(),
            )) {
                throw new \DomainException('Valeur forfaitaire ujn non trouvée');
            }
            return $ujn;
        });
    }

    /** @inheritdoc */
    public function u(): float
    {
        return $this->ujn();
    }

    /**
     * Etat de performance de la baie
     * 
     * @see Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine
     */
    public function performance(): Performance
    {
        return $this->get('performance', function () {
            $u = $this->u();
            return match (true) {
                $u >= 3 => Performance::INSUFFISANTE,
                $u >= 2.2 => Performance::MOYENNE,
                $u >= 1.6 => Performance::BONNE,
                $u < 1.6 => Performance::TRES_BONNE,
            };
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->baies() as $paroi) {
            $this->clear();
            $this->paroi = $paroi;

            $paroi->calcule($paroi->data()->with(
                sdep: $this->sdep(),
                ug: $this->ug(),
                uw: $this->uw(),
                u: $this->u(),
                b: $this->b(),
                dp: $this->dp(),
                performance: $this->performance(),
            ));

            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
                type: TypeDeperdition::BAIE,
                deperdition: $this->dp(),
            )));
        }
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [DeperditionBaieDoubleFenetre::class];
    }
}
