<?php

namespace App\Domain\Document;

interface DocumentRepository
{
    public function find(\Stringable $id): ?Document;
    public function search(\Stringable $id_audit): DocumentCollection;
}
