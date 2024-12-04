<?php

namespace App\Api\PlancherBas\Payload;

use App\Domain\PlancherBas\Enum\{EtatIsolation, TypeIsolation};
use App\Domain\PlancherBas\ValueObject\Isolation;
use Symfony\Component\Validator\Constraints as Assert;

final class IsolationPayload
{
    public function __construct(
        public EtatIsolation $etat_isolation,
        public ?TypeIsolation $type_isolation,
        public ?int $annee_isolation,
        #[Assert\Positive]
        public ?int $epaisseur_isolation,
        #[Assert\Positive]
        public ?float $resistance_thermique_isolation,
    ) {}

    public function to(): Isolation
    {
        return match ($this->etat_isolation) {
            EtatIsolation::INCONNU => Isolation::create_inconnu(),
            EtatIsolation::NON_ISOLE => Isolation::create_non_isole(),
            EtatIsolation::ISOLE => Isolation::create_isole(
                type_isolation: $this->type_isolation ?? TypeIsolation::INCONNU,
                annee_isolation: $this->annee_isolation,
                epaisseur_isolation: $this->epaisseur_isolation,
                resistance_thermique_isolation: $this->resistance_thermique_isolation,
            ),
        };
    }
}
