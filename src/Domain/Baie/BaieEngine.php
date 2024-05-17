<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Engine\DoubleFenetreEngine;
use App\Domain\Baie\Enum\{Mitoyennete, NatureGazLame, NatureMenuiserie, QualiteComposant, TypeBaie, TypeFermeture, TypePose, TypeVitrage};
use App\Domain\Baie\Table\{B, BRepository};
use App\Domain\Baie\Table\{C1, C1Collection, C1Repository, Sw, SwRepository};
use App\Domain\Baie\Table\{UgCollection, UgRepository, UwCollection, UwRepository};
use App\Domain\Baie\Table\{Deltar, DeltarRepository, UjnCollection, UjnRepository};
use App\Domain\Baie\ValueObject\DoubleFenetre;
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\{Enum, Mois, Orientation};
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.3 - Calcul des U des parois vitrées et des portes
 * @see §6.2 - Détermination de la surface Sud équivalente
 */
final class BaieEngine
{
    private SimulationEngine $context;
    private Baie $input;

    private ?B $table_b;
    private UgCollection $table_ug_collection;
    private UwCollection $table_uw_collection;
    private ?Deltar $table_deltar;
    private UjnCollection $table_ujn_collection;
    private ?Sw $table_sw;
    private C1Collection $table_c1_collection;

    public function __construct(
        private DoubleFenetreEngine $double_fenetre_engine,
        private BRepository $table_b_repository,
        private UgRepository $table_ug_repository,
        private UwRepository $table_uw_repository,
        private DeltarRepository $table_deltar_repository,
        private UjnRepository $table_ujn_repository,
        private SwRepository $table_sw_repository,
        private C1Repository $table_c1_repository,
    ) {
    }

    /**
     * Surface sud équivalente (m²)
     */
    public function sse(): float
    {
        return \array_reduce(Mois::cases(), fn (float $sse, Mois $mois) => $sse + $this->sse_j($mois), 0);
    }

    /**
     * Surface sud équivalente pour le mois j (m²)
     */
    public function sse_j(Mois $mois): float
    {
        return $this->calcul_sse_veranda()
            ? $this->ssd_j($mois) + $this->ssind_j($mois) * $this->b()
            : $this->sdep() * $this->sw() * $this->fe() * $this->c1_j($mois);
    }

    /**
     * Surface sud équivalente représentant les apports solaires indirects dans le logement pour le mois j (m²)
     */
    public function ssind_j(Mois $mois): float
    {
        return \max($this->sst_j($mois) - $this->ssd_j($mois), 0);
    }

    /**
     * Surface sud équivalente des apports dans la véranda pour le mois j
     */
    public function sst_j(Mois $mois): float
    {
        return $this->calcul_sse_veranda()
            ? $this->context->local_non_chauffe_engine_collection()->sst_j(id: $this->local_non_chauffe_id(), mois: $mois)
            : 0;
    }

    /**
     * Surface sud équivalente représentant l'impact des apports solaires associés au rayonnement
     * solaire traversant directement l'espace tampon pour arriver dans la partie habitable du logement (m²)
     */
    public function ssd_j(Mois $mois): float
    {
        return $this->calcul_sse_veranda()
            ? $this->sdep() * $this->sw() * $this->fe() * $this->c1_j($mois) * $this->t()
            : 0;
    }

    /**
     * DP,baie - Déperditions thermiques (W/K)
     */
    public function dp(): float
    {
        return $this->u() * $this->sdep() * $this->b();
    }

    /**
     * u,baie - Coefficient de transmission thermique (W/(m².K))
     */
    public function u(): float
    {
        return $this->ujn();
    }

    /**
     * Ujn - Coefficient de transmission thermique avec les protections solaires (W/(m².K))
     */
    public function ujn(): float
    {
        if (false === $this->calcul_ujn()) {
            return $this->uw();
        }
        if ($this->ujn_saisi()) {
            return $this->ujn_saisi();
        }
        if (0 === $this->table_ujn_collection()->count()) {
            throw new EngineTableError('baie . ujn');
        }
        return $this->table_ujn_collection()->ujn(uw: $this->uw());
    }

    /**
     * ΔR - Résistance additionnelle due à la présence de fermeture (m2.K/W)
     */
    public function deltar(): float
    {
        if (false === $this->calcul_ujn()) {
            return 0;
        }
        if (null === $this->table_deltar()) {
            throw new EngineTableError('baie . deltar');
        }
        return $this->table_deltar()->valeur();
    }

    /**
     * Uw - Coefficient de transmission thermique (vitrage + menuiserie) (W/(m².K))
     */
    public function uw(): float
    {
        return $this->double_fenetre() ? 1 / (1 / $this->uw1() + 1 / $this->uw2() + 0.07) : $this->uw1();
    }

    /**
     * Uw1 - Coefficient de transmission thermique (vitrage + menuiserie) (W/(m².K))
     */
    public function uw1(): float
    {
        if ($this->uw_saisi()) {
            return $this->uw_saisi();
        }
        if (0 === $this->table_uw_collection()->count()) {
            throw new EngineTableError('baie . uw');
        }
        return $this->table_uw_collection()->uw(ug: $this->ug());
    }

    /**
     * Uw2 - Coefficient de transmission thermique de la double fenêtre (vitrage + menuiserie) (W/(m².K))
     */
    public function uw2(): float
    {
        return $this->double_fenetre() ? $this->double_fenetre_engine()->uw() : 1;
    }

    /**
     * Ug - Coefficient de transmission thermique du vitrage (W/(m².K))
     */
    public function ug(): float
    {
        if ($this->ug_saisi()) {
            return $this->ug_saisi();
        }
        if (0 === $this->table_ug_collection()->count()) {
            throw new EngineTableError('baie . ug');
        }
        return $this->table_ug_collection()?->ug(
            epaisseur_lame: $this->epaisseur_lame_gaz() ?? 0
        );
    }

    /**
     * Sw - Proportion d'énergie solaire incidente qui pénètre dans le logement par la paroi vitrée
     */
    public function sw(): float
    {
        return $this->sw1() * $this->sw2();
    }

    /**
     * Sw1 - Proportion d'énergie solaire incidente qui pénètre dans le logement par la paroi vitrée
     */
    public function sw1(): float
    {
        if ($this->sw_saisi()) {
            return $this->sw_saisi();
        }
        if (null === $this->table_sw()) {
            throw new EngineTableError('baie . sw');
        }
        return $this->table_sw()->valeur();
    }

    /**
     * Sw2 - Proportion d'énergie solaire incidente qui pénètre dans le logement par la double fenêtre
     */
    public function sw2(): float
    {
        return $this->double_fenetre() ? $this->double_fenetre_engine()->sw() : 1;
    }

    /**
     * Fe - Facteur de réduction de l'ensoleillement due aux masques
     */
    public function fe(): float
    {
        return $this->fe1() * $this->fe2();
    }

    /**
     * Fe1 - Facteur de réduction de l'ensoleillement due aux masques proches
     */
    public function fe1(): float
    {
        return $this->context->masque_proche_engine_collection()->fe1(baie_id: $this->id());
    }

    /**
     * Fe2 - Facteur de réduction de l'ensoleillement due aux masques loitains
     */
    public function fe2(): float
    {
        return $this->context->masque_lointain_engine_collection()->fe2(orientation: $this->orientation());
    }

    /**
     * Coefficient de transparence de la véranda
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function t(): float
    {
        if (null === $this->local_non_chauffe_id()) {
            return 1;
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->t($this->local_non_chauffe_id())) {
            throw new EngineValeurError('baie . t');
        }
        return $value;
    }

    /**
     * b,paroi - Coefficient de réduction thermique
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function b(): float
    {
        if (null === $this->input()->local_non_chauffe()) {
            if (null === $this->table_b()) {
                throw new EngineTableError('baie . b');
            }
            return $this->table_b()->valeur();
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->b($this->local_non_chauffe_id())) {
            throw new EngineValeurError('baie . b');
        }
        return $value;
    }

    /**
     * C1,j - Coefficient d'orientation et d'inclinaison pour le mois j
     */
    public function c1_j(Mois $mois): float
    {
        return $this->table_c1($mois)->c1;
    }

    /**
     * Surface déperditive (m²)
     */
    public function sdep(): float
    {
        return $this->surface_deperditive();
    }

    /**
     * Indicateur de performance de l'élément
     */
    public function qualite_isolation(): QualiteComposant
    {
        return QualiteComposant::from_ubaie($this->u());
    }

    /**
     * Scénario de calcul des surfaces sud équivalentes en présence d'une véranda non chauffée
     */
    public function calcul_sse_veranda(): bool
    {
        if (null === $this->local_non_chauffe_id()) {
            return false;
        }
        if (null === $engine = $this->context->local_non_chauffe_engine_collection()->get($this->local_non_chauffe_id())) {
            throw new EngineValeurError('lnc');
        }
        return $engine->input()->ets();
    }

    /**
     * Prise en compte de ujn pour le calcul de u
     */
    public function calcul_ujn(): bool
    {
        return $this->type_fermeture() !== TypeFermeture::SANS_FERMETURE;
    }

    /**
     * Déperditions thermiques de la double feneêtre
     */
    public function double_fenetre_engine(): ?DoubleFenetreEngine
    {
        return $this->double_fenetre() ? $this->double_fenetre_engine : null;
    }

    /**
     * Valeur de la table paroi . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    /**
     * Valeurs de la table baie . ug
     */
    public function table_ug_collection(): UgCollection
    {
        return $this->table_ug_collection;
    }

    /**
     * Valeurs de la table baie . uw
     */
    public function table_uw_collection(): UwCollection
    {
        return $this->table_uw_collection;
    }

    /**
     * Valeur de la table baie . deltar
     */
    public function table_deltar(): ?Deltar
    {
        return $this->table_deltar;
    }

    /**
     * Valeurs de la table baie . ujn
     */
    public function table_ujn_collection(): UjnCollection
    {
        return $this->table_ujn_collection;
    }

    /**
     * Valeur de la table baie . sw
     */
    public function table_sw(): ?Sw
    {
        return $this->table_sw;
    }

    /**
     * Valeur de la table baie . c1 pour le mois j
     */
    public function table_c1(Mois $mois): C1
    {
        if (null === $value = $this->table_c1_collection()->find($mois)) {
            throw new EngineTableError('baie . c1');
        }
        return $value;
    }

    /**
     * Valeurs de la table baie . c1
     */
    public function table_c1_collection(): C1Collection
    {
        return $this->table_c1_collection;
    }

    public function fetch(): void
    {
        $this->table_b = $this->table_b_repository->find_by(mitoyennete: $this->mitoyennete());

        $this->table_ug_collection = $this->table_ug_repository->search_by(
            type_vitrage: $this->type_vitrage(),
            nature_gaz_lame: $this->nature_gaz_lame(),
            inclinaison_vitrage: $this->inclinaison_vitrage(),
        );

        $this->table_uw_collection = $this->table_uw_repository->search_by(
            type_baie: $this->type_baie(),
            nature_menuiserie: $this->nature_menuiserie(),
        );

        $this->table_deltar = $this->calcul_ujn() ? $this->table_deltar_repository->find_by(
            type_fermeture: $this->type_fermeture(),
        ) : null;

        $this->table_ujn_collection = $this->table_deltar
            ? $this->table_ujn_repository->search_by(deltar: $this->table_deltar())
            : new UjnCollection;

        $this->table_sw = $this->table_sw_repository->find_by(
            type_baie: $this->type_baie(),
            nature_menuiserie: $this->nature_menuiserie(),
            type_pose: $this->type_pose(),
            type_vitrage: $this->type_vitrage(),
        );

        $this->table_c1_collection = $this->table_c1_repository->search_by(
            zone_climatique: $this->zone_climatique(),
            orientation: $this->orientation(),
            inclinaison: $this->inclinaison_vitrage(),
        );

        $this->double_fenetre_engine = $this->double_fenetre()
            ? ($this->double_fenetre_engine)($this->double_fenetre(), $this)
            : $this->double_fenetre_engine;
    }

    // * Données d'entrée

    public function id(): Id
    {
        return $this->input->id();
    }

    public function local_non_chauffe_id(): ?Id
    {
        return $this->input->local_non_chauffe()?->id();
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input()->enveloppe()->batiment()->adresse()->zone_climatique;
    }

    public function mitoyennete(): Mitoyennete|Enum
    {
        return $this->input()->mitoyennete();
    }

    public function orientation(): ?Orientation
    {
        return $this->input()->orientation()?->enum();
    }

    public function nature_menuiserie(): NatureMenuiserie
    {
        return $this->input()->caracteristique()->nature_menuiserie;
    }

    public function type_baie(): TypeBaie
    {
        return $this->input()->caracteristique()->type_baie;
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->input()->caracteristique()->type_vitrage;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        return $this->input()->caracteristique()->nature_gaz_lame;
    }

    public function type_fermeture(): TypeFermeture
    {
        return $this->input()->caracteristique()->type_fermeture;
    }

    public function type_pose(): TypePose
    {
        return $this->input()->caracteristique()->type_pose;
    }

    public function epaisseur_lame_gaz(): ?float
    {
        return $this->input()->caracteristique()->epaisseur_lame?->valeur();
    }

    public function surface_deperditive(): float
    {
        return $this->input()->surface_deperditive();
    }

    public function inclinaison_vitrage(): float
    {
        return $this->input()->caracteristique()->inclinaison_vitrage->valeur();
    }

    public function ug_saisi(): ?float
    {
        return $this->input()->caracteristique()->ug?->valeur();
    }

    public function uw_saisi(): ?float
    {
        return $this->input()->caracteristique()->uw?->valeur();
    }

    public function ujn_saisi(): ?float
    {
        return $this->input()->caracteristique()->ujn?->valeur();
    }

    public function sw_saisi(): ?float
    {
        return $this->input()->caracteristique()->sw?->valeur();
    }

    public function double_fenetre(): ?DoubleFenetre
    {
        return $this->input()->double_fenetre();
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function input(): Baie
    {
        return $this->input;
    }

    public function __invoke(Baie $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
