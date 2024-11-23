<?php

namespace App\Domain\Baie\Data;

interface UjnRepository
{
    public function search_by(float $deltar): UjnCollection;
}
