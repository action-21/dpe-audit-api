<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Chauffage\Entity\{Installation, Systeme};
use App\Domain\Common\Enum\Enum;

enum Configuration: string implements Enum
{
    case BASE = 'base';
    case BASE_APPOINT = 'base_appoint';
    case BASE_BOIS_RELEVE_PAC = 'base_bois_releve_pac';
    case BASE_BOIS_RELEVE_CHAUDIERE = 'base_bois_releve_chaudiere';
    case BASE_PAC_RELEVE_CHAUDIERE = 'base_pac_releve_chaudiere';
    case AUTRES = 'autres';

    public static function determine(Installation $entity): self
    {
        $systemes_chauffage_central = $entity->installation_collective()
            ? $entity->systemes()->with_systeme_collectif()->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            : $entity->systemes()->with_type(TypeChauffage::CHAUFFAGE_CENTRAL);

        $is_chauffage_central = $systemes_chauffage_central->has_systeme_central();
        $has_pac = $systemes_chauffage_central->has_pac();
        $has_chaudiere = $systemes_chauffage_central->has_chaudiere();
        $has_chaudiere_bois = $systemes_chauffage_central->has_chaudiere_bois();
        $has_releve = $is_chauffage_central && $systemes_chauffage_central->count() > 1;
        $has_appoint = $is_chauffage_central && $entity->systemes()->has_systeme_divise();

        return match (true) {
            $is_chauffage_central && !$has_releve && !$has_appoint => self::BASE,
            $is_chauffage_central && !$has_releve && $has_appoint => self::BASE_APPOINT,
            $has_chaudiere_bois && $has_pac => self::BASE_BOIS_RELEVE_PAC,
            $has_chaudiere_bois && $has_chaudiere => self::BASE_BOIS_RELEVE_CHAUDIERE,
            $has_pac && $has_chaudiere => self::BASE_PAC_RELEVE_CHAUDIERE,
            default => self::AUTRES,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BASE => 'Chauffage central simple',
            self::BASE_APPOINT => 'Chauffage central avec base + appoint',
            self::BASE_BOIS_RELEVE_PAC => 'Chauffage central avec chaudière bois + PAC',
            self::BASE_BOIS_RELEVE_CHAUDIERE => 'Chauffage central avec chaudière bois + chaudière',
            self::BASE_PAC_RELEVE_CHAUDIERE => 'Chauffage central avec PAC + chaudière',
            self::AUTRES => 'Autres configurations',
        };
    }

    public function is_base(Systeme $entity): bool
    {
        return match ($this) {
            self::BASE_BOIS_RELEVE_PAC => $entity->generateur()->type()->is_chaudiere() && $entity->generateur()->energie()->is_bois(),
            self::BASE_BOIS_RELEVE_CHAUDIERE => $entity->generateur()->signaletique()->type->is_chaudiere(),
            self::BASE_PAC_RELEVE_CHAUDIERE => $entity->generateur()->signaletique()->type->is_pac() && $entity->type_chauffage() === TypeChauffage::CHAUFFAGE_CENTRAL,
            self::BASE, self::BASE_APPOINT => $entity->type_chauffage() === TypeChauffage::CHAUFFAGE_CENTRAL,
            self::AUTRES => true,
        };
    }

    public function is_releve(Systeme $entity): bool
    {
        return match ($this) {
            self::BASE_BOIS_RELEVE_PAC => $entity->generateur()->signaletique()->type->is_pac() && $entity->type_chauffage() === TypeChauffage::CHAUFFAGE_CENTRAL,
            self::BASE_BOIS_RELEVE_CHAUDIERE, self::BASE_PAC_RELEVE_CHAUDIERE => $entity->generateur()->signaletique()->type->is_chaudiere(),
            default => false,
        };
    }

    public function is_appoint(Systeme $entity): bool
    {
        return match ($this) {
            self::BASE => false,
            self::AUTRES => false,
            default => $entity->type_chauffage() === TypeChauffage::CHAUFFAGE_DIVISE,
        };
    }

    public function configuration_collective(Installation $entity): bool
    {
        return match ($this) {
            self::BASE_APPOINT,
            self::BASE_BOIS_RELEVE_PAC,
            self::BASE_BOIS_RELEVE_CHAUDIERE,
            self::BASE_PAC_RELEVE_CHAUDIERE => $entity->installation_collective(),
            default => false,
        };
    }
}
