<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum ConfigurationSysteme: string implements Enum
{
    case BASE = 'base';
    case RELEVE = 'releve';
    case APPOINT = 'appoint';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BASE => 'Système en base',
            self::RELEVE => 'Système en relève',
            self::APPOINT => 'Système d\'appoint',
        };
    }

    public function is(self $configuration): bool
    {
        return $this === $configuration;
    }
}
