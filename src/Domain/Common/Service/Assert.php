<?php

namespace App\Domain\Common\Service;

final class Assert
{
    public static function null(mixed $valeur): void
    {
        if (null !== $valeur)
            throw new \InvalidArgumentException("Valeur {$valeur} non nulle");
    }

    public static function non_null(mixed $valeur): void
    {
        if (null === $valeur)
            throw new \InvalidArgumentException("Valeur {$valeur} nulle");
    }

    public static function egal(mixed $valeur, mixed $comparant): void
    {
        if ($valeur !== $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} différente de {$comparant}");
    }

    public static function different(mixed $valeur, mixed $comparant): void
    {
        if ($valeur === $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} différente de {$comparant}");
    }

    public static function positif(null|int|float $valeur): void
    {
        if (null !== $valeur && $valeur <= 0)
            throw new \InvalidArgumentException("Valeur {$valeur} inférieure ou égale à 0");
    }

    public static function positif_ou_zero(null|int|float $valeur): void
    {
        if (null !== $valeur && $valeur < 0)
            throw new \InvalidArgumentException("Valeur {$valeur} inférieure à 0");
    }

    public static function superieur_a(null|int|float $valeur, int|float $comparant): void
    {
        if (null !== $valeur && $valeur <= $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} inférieure ou égale à {$comparant}");
    }

    public static function superieur_ou_egal_a(null|int|float $valeur, int|float $comparant): void
    {
        if (null !== $valeur && $valeur < $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} inférieure à {$comparant}");
    }

    public static function inferieur_a(null|int|float $valeur, int|float $comparant): void
    {
        if (null !== $valeur && $valeur >= $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} supérieure ou égale à {$comparant}");
    }

    public static function inferieur_ou_egal_a(null|int|float $valeur, int|float $comparant): void
    {
        if (null !== $valeur && $valeur > $comparant)
            throw new \InvalidArgumentException("Valeur {$valeur} supérieure à {$comparant}");
    }

    public static function non_vide(\Countable $array): void
    {
        if (0 === count($array))
            throw new \InvalidArgumentException("Tableau vide");
    }

    public static function enums(mixed $valeur, array $enumerations): void
    {
        if (!\in_array($valeur, \array_column($enumerations, 'value')))
            throw new \InvalidArgumentException("Valeur {$valeur} non applicable");
    }

    public static function annee(null|int $valeur): void
    {
        if (null === $valeur)
            return;

        self::inferieur_a($valeur, (int) date('Y'));
    }

    public static function orientation(null|float|int $valeur): void
    {
        if (null === $valeur)
            return;

        self::positif_ou_zero($valeur);
        self::inferieur_a($valeur, 360);
    }

    public static function inclinaison(null|float|int $valeur): void
    {
        if (null === $valeur)
            return;

        self::positif_ou_zero($valeur);
        self::inferieur_ou_egal_a($valeur, 90);
    }
}
