<?php

namespace App\Command;

use App\Domain\Audit\AuditRepository;
use App\Domain\Audit\Enum\PeriodeConstruction;
use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Id;
use App\Services\Observatoire\Observatoire;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:import',
    description: 'Importe des audits depuis l\'observatoire DPE-Audit',
    hidden: false,
)]
final class ImportCommand extends Command
{
    public function __construct(
        private readonly AuditRepository $repository,
        private readonly Observatoire $observatoire,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($search = $input->getArgument('search')) {
            $id = Id::from($search);
            $content = $this->observatoire->find($id);
            dd($content);
            $this->save($id, $content);
            return Command::SUCCESS;
        };

        dd('exit');

        $repository = $this->repository
            ->with_type_batiment(TypeBatiment::MAISON)
            ->sort('date_etablissement_dpe');

        foreach (ZoneClimatique::cases() as $zone_climatique) {
            $repository->with_zone_climatique($zone_climatique);

            foreach (PeriodeConstruction::cases() as $periode_construction) {
                $repository->with_periode_construction($periode_construction);

                foreach ($repository->search() as $audit) {
                    $id = $audit->id();
                    $output->writeln("Importing audit {$id}...");

                    if (null === $content = $this->observatoire->find($audit->id())) {
                        $output->writeln("Failed");
                        continue;
                    }
                    $this->save($id, $content);
                    $output->writeln("Done");
                }
            }
        }
        return Command::SUCCESS;
    }

    private function save(Id $id, string $content): void
    {
        $path = "{$this->projectDir}/data/audits/{$id}.xml";
        $xml = \simplexml_load_string($content);
        $xml->saveXML($path);
    }

    protected function configure(): void
    {
        $this->addArgument('search', InputArgument::OPTIONAL, 'Numéro de DPE :');
    }
}
