<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum ConfigurationInstallation: string implements Enum
{
    case BASE = 'base';
    case BASE_APPOINT = 'base_appoint';
    case BASE_BOIS_RELEVE_PAC = 'base_bois_releve_pac';
    case BASE_BOIS_RELEVE_CHAUDIERE = 'base_bois_releve_chaudiere';
    case BASE_PAC_RELEVE_CHAUDIERE = 'base_pac_releve_chaudiere';
    case AUTRES = 'autres';

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
}
