<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\{PaginatorInterface, Pagination};
use App\Api\Audit\Collection\{Audit, AuditPaginator};
use App\Api\Audit\SearchAuditHandler;
use App\Api\Audit\Query\SearchQuery;
use App\Domain\Audit\Enum\{Etiquette, ClasseAltitude, PeriodeConstruction, TypeBatiment};
use App\Domain\Common\Enum\ZoneClimatique;

/**
 * @implements ProviderInterface<Audit|null>
 */
final class AuditCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly SearchAuditHandler $handler,
        private readonly Pagination $pagination,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PaginatorInterface
    {
        $query = new SearchQuery(
            page: $context['filters']['page'] ?? 1,
            date_etablissement_min: $context['filters']['date_etablissement_min'] ?? null,
            date_etablissement_max: $context['filters']['date_etablissement_max'] ?? null,
            surface_habitable_min: $context['filters']['surface_habitable_min'] ?? null,
            surface_habitable_max: $context['filters']['surface_habitable_max'] ?? null,
            type_batiment: array_map(
                fn(string $item) => TypeBatiment::from($item),
                $context['filters']['type_batiment'] ?? [],
            ),
            etiquette_energie: array_map(
                fn(string $item) => Etiquette::from($item),
                $context['filters']['etiquette_energie'] ?? [],
            ),
            etiquette_climat: array_map(
                fn(string $item) => Etiquette::from($item),
                $context['filters']['etiquette_climat'] ?? [],
            ),
            zone_climatique: array_map(
                fn(string $item) => ZoneClimatique::from($item),
                $context['filters']['zone_climatique'] ?? [],
            ),
            code_postal: $context['filters']['code_postal'] ?? [],
            code_departement: $context['filters']['code_departement'] ?? [],
        );

        if ($context['filters']['periode_construction'] ?? null) {
            $query->with_periode_construction(PeriodeConstruction::from($context['filters']['periode_construction']));
        }
        if ($context['filters']['classe_altitude'] ?? null) {
            $query->with_classe_altitude(ClasseAltitude::from($context['filters']['classe_altitude']));
        }

        $handle = $this->handler;

        return new AuditPaginator(
            collection: $handle($query),
            currentPage: $query->page,
            itemsPerPage: $query->itemsPerPage,
            totalItems: $handle->count(),
        );
    }
}
