<?php

namespace App\Domain\Common\Enum;

enum MethodeSaisie: int implements Enum
{
    case VALEUR_DEFAUT = 1;
    case MESURE_OBSERVATION = 2;
    case DOCUMENTS_JUSTIFICATIFS = 3;
    case DONNEES_PUBLIQUES = 4;
    case ESTIMATION = 5;
    case VALEUR_DEFAUT_PENALISANTE = 6;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::VALEUR_DEFAUT => 'Valeur par défaut',
            self::MESURE_OBSERVATION => 'Mesure ou observation',
            self::DOCUMENTS_JUSTIFICATIFS => 'Documents justificatifs',
            self::DONNEES_PUBLIQUES => 'Données publiques',
            self::ESTIMATION => 'Estimation de l\'auditeur',
            self::VALEUR_DEFAUT_PENALISANTE => 'Valeur par défaut pénalisante',
        };
    }
}
