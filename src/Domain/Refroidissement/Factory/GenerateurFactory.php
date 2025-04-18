<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Data\GenerateurData;
use App\Domain\Refroidissement\Entity\{Generateur, ReseauFroid};
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Repository\ReseauFroidRepository;
use Webmozart\Assert\Assert;

abstract class GenerateurFactory
{
    protected Id $id;
    protected Refroidissement $refroidissement;
    protected string $description;
    protected TypeGenerateur $type;
    protected EnergieGenerateur $energie;
    protected ?Annee $annee_installation = null;
    protected ?float $seer = null;
    protected ?ReseauFroid $reseau_froid = null;

    public function __construct(protected readonly ReseauFroidRepository $reseau_froid_repository) {}

    public function initialize(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        ?Annee $annee_installation,
    ): self {
        $this->id = $id;
        $this->refroidissement = $refroidissement;
        $this->description = $description;
        $this->type = $type;
        $this->energie = $energie;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    public function set_seer(float $seer): self
    {
        Assert::greaterThan($seer, 0);
        $this->seer = $seer;
        return $this;
    }

    public function set_reseau_froid(Id $reseau_froid_id): self
    {
        Assert::notNull($reseau_froid = $this->reseau_froid_repository->find($reseau_froid_id));
        $this->reseau_froid = $reseau_froid;
        return $this;
    }

    public function build(): Generateur
    {
        return new Generateur(
            id: $this->id,
            refroidissement: $this->refroidissement,
            description: $this->description,
            type: $this->type,
            energie: $this->energie,
            seer: $this->seer,
            annee_installation: $this->annee_installation,
            reseau_froid: $this->reseau_froid,
            data: GenerateurData::create(),
        );
    }

    abstract public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool;
}
