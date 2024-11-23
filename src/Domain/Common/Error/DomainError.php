<?php

namespace App\Domain\Common\Error;

final class DomainError extends \DomainException
{
    /**
     * @throws DomainError
     * @deprecated
     */
    public static function donne_intermediaire_non_definie(string $nom): void
    {
        throw new self("Valeur forfaitaire {$nom} non trouvée");
    }

    /**
     * @throws DomainError
     * @deprecated
     */
    public static function valeur_forfaitaire_non_trouvee(string $nom): void
    {
        throw new self("Valeur forfaitaire {$nom} non trouvée");
    }

    /**
     * @throws DomainError
     */
    public static function valeur_forfaitaire(string $nom): void
    {
        throw new self("Valeur forfaitaire {$nom} non trouvée");
    }
}
