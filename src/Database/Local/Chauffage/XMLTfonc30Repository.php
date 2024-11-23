<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Tfonc30, Tfonc30Repository};
use App\Domain\Chauffage\Enum\{CategorieGenerateur, TemperatureDistribution};

final class XMLTfonc30Repository implements Tfonc30Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.tfonc30';
    }

    public function find_by(
        CategorieGenerateur $categorie_generateur,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_generateur,
        int $annee_installation_emetteur,
    ): ?Tfonc30 {
        $record = $this->createQuery()
            ->and('categorie_generateur', $categorie_generateur->value)
            ->and('temperature_distribution', $temperature_distribution->value)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Tfonc30
    {
        return new Tfonc30(tfonc30: $element->get('tfonc30')->floatval());
    }
}
