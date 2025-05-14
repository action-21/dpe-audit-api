<?php

namespace App\Command;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Database\Opendata\ObservatoireDPEAuditSearcher;
use App\Domain\Audit\Enum\PeriodeConstruction;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Id;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:import',
    description: 'Importe aléatoirement des audits depuis l\'observatoire DPE-Audit',
    hidden: false,
)]
final class ImportCommand extends Command
{
    public final const BASE_URL = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe-v2-logements-existants/lines';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ObservatoireDPEAuditSearcher $searcher,
        private readonly ObservatoireDPEAuditFinder $finder,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $searcher = $this->searcher
            ->addQuery('sort', 'date_etablissement_dpe')
            ->addQuery('type_batiment_eq', 'maison')
            ->randomize();

        foreach (ZoneClimatique::cases() as $zone_climatique) {
            $searcher->addQuery('zone_climatique__eq', $zone_climatique->value);

            foreach (PeriodeConstruction::cases() as $periode_construction) {
                $searcher->addQuery('periode_construction_eq', $periode_construction->value);

                foreach ($searcher->search() as $result) {
                    $id = $result['numero_dpe'];
                    $output->writeln("Importing audit {$id}...");

                    if (null === $xml = $this->finder->find(Id::from($id))) {
                        $output->writeln("Failed");
                        continue;
                    }
                    $xml->saveXML("{$this->projectDir}/data/audits/{$id}.xml");
                    $output->writeln("Done");
                }
            }
        }
        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    public function queries(): array
    {
        $queries = [];

        return $queries;
    }
}
