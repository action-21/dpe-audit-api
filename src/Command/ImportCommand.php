<?php

namespace App\Command;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Audit\Enum\PeriodeConstruction;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Type\Id;
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
        private HttpClientInterface $client,
        private XMLOpendataRepository $observatoire_repository,
        private string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (ZoneClimatique::cases() as $zone_climatique) {
            foreach (PeriodeConstruction::cases() as $periode_construction) {
                $random_date = \rand((new \DateTime('2021-07-01'))->getTimestamp(), (new \DateTime)->getTimestamp(),);
                $random_date = date("Y-m-d", $random_date);
                $size = 5;

                $response = $this->client->request('GET', self::BASE_URL, [
                    'query' => [
                        'Date_établissement_DPE_gte' => $random_date,
                        'Version_DPE_in' => '2.2,2.3,2.4',
                        'Type_bâtiment_eq' => 'maison',
                        'Zone_climatique__eq' => $zone_climatique->value,
                        'Période_construction_eq' => $periode_construction->filter(),
                        'size' => $size,
                        'sort' => 'Date_établissement_DPE',
                        'select' => 'N°DPE',
                    ],
                ]);

                if ($response->getStatusCode() !== 200) {
                    return Command::FAILURE;
                }

                foreach ($response->toArray()['results'] as $line) {
                    $id = $line['N°DPE'];
                    $output->writeln("Importing audit {$id}...");
                    if (null === $xml = $this->observatoire_repository->find(Id::from($id))) {
                        $output->writeln("Failed");
                        continue;
                    }
                    $xml->saveXML("{$this->projectDir}/etc/audits/{$id}.xml");
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
