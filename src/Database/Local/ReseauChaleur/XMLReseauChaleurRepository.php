<?php

namespace App\Database\Local\ReseauChaleur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Type\Id;
use App\Domain\ReseauChaleur\ReseauChaleur;
use App\Domain\ReseauChaleur\ReseauChaleurRepository;

final class XMLReseauChaleurRepository implements ReseauChaleurRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'reseau_chaleur';
    }

    public function find(Id $id): ?ReseauChaleur
    {
        $record = $this->createQuery()->and('id', (string) $id)->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): ReseauChaleur
    {
        return new ReseauChaleur(
            id: Id::from($element->get('id')->strval()),
            code_departement: $element->get('code_departement')->strval(),
            localisation: $element->get('localisation')->strval(),
            nom: $element->get('nom')->strval(),
            contenu_co2: $element->get('contenu_co2')->floatval(),
            contenu_co2_acv: $element->get('contenu_co2_acv')->floatval(),
            taux_enr: $element->get('taux_enr')->floatval(),
        );
    }
}
