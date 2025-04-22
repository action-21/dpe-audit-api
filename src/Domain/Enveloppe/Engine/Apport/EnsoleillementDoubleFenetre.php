<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;

final class EnsoleillementDoubleFenetre extends EngineRule
{
    private DoubleFenetre $double_fenetre;

    public function __construct(private readonly BaieTableValeurRepository $table_repository) {}

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

    public function type_survitrage(): ?TypeSurvitrage
    {
        if (null === $this->double_fenetre->vitrage()?->survitrage) {
            return null;
        }
        if ($this->double_fenetre->vitrage()?->survitrage?->type_survitrage) {
            return $this->double_fenetre->vitrage()->type_vitrage;
        }
        if (null !== $this->double_fenetre->type_baie()->is_paroi_vitree()) {
            return TypeSurvitrage::SURVITRAGE_SIMPLE;
        }
        return null;
    }

    /**
     * Proportion d'énergie solaire reçue par la double fenêtre
     */
    public function sw(): Pourcentage
    {
        if ($this->double_fenetre->performance()->sw) {
            return $this->double_fenetre->performance()->sw;
        }
        if (null === $sw = $this->table_repository->sw(
            type_baie: $this->double_fenetre->type_baie(),
            type_pose: $this->double_fenetre->type_pose(),
            presence_soubassement: $this->double_fenetre->presence_soubassement(),
            materiau: $this->materiau(),
            type_vitrage: $this->type_vitrage(),
            type_survitrage: $this->type_survitrage(),
        )) {
            throw new \DomainException("Valeur forfaitaire sw non trouvée");
        }
        return $sw;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            if (null === $baie->double_fenetre()) {
                continue;
            }
            $this->double_fenetre = $baie->double_fenetre();
            $baie->double_fenetre()->calcule($baie->double_fenetre()->data()->with(
                sw: $this->sw()
            ));
        }
    }
}
