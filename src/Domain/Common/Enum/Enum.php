<?php

namespace App\Domain\Common\Enum;

interface Enum
{
    public function id(): int|string;
    public function lib(): string;
}
