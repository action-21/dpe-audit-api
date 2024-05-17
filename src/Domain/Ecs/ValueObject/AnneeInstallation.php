<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Entier;
use App\Domain\Ecs\Enum\TypeGenerateur;

/**
 * Année d'installation du générateur d'ECS
 */
final class AnneeInstallation extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }

    public static function from_enum_periode_installation_ecs_thermo_id(int $id): self
    {
        return match ($id) {
            1 => static::from(2009),
            2 => static::from(2014),
            3 => static::from(\date("Y")),
        };
    }

    public static function is_requis_by_type_generateur(TypeGenerateur $type_generateur): bool
    {
        return !\in_array($type_generateur, [
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
            TypeGenerateur::RESEAU_CHALEUR_NON_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_ISOLE,
            TypeGenerateur::CHAUDIERE_BOIS_MULTI_BATIMENT,
            TypeGenerateur::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            TypeGenerateur::CHAUDIERE_GAZ_MULTI_BATIMENT,
            TypeGenerateur::POMPE_CHALEUR_MULTI_BATIMENT,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
            TypeGenerateur::SYSTEME_COLLECTIF_DEFAUT,
            TypeGenerateur::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE,
            TypeGenerateur::CHAUDIERE_ELECTRIQUE,
            TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
            TypeGenerateur::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        ]);
    }
}
