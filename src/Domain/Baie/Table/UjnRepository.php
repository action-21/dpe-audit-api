<?php

namespace App\Domain\Baie\Table;

interface UjnRepository
{
    public function search(int $id): UjnCollection;
    public function search_by(Deltar $deltar): UjnCollection;
}
