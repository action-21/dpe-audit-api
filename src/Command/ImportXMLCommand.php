<?php

namespace App\Command;

use App\Application\Audit\AuditView;
use App\Database\Opendata\Audit\XMLAuditParser;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\AuditEngine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import:local',
    description: 'Importe des audits XML localement',
    hidden: false,
)]
class ImportXMLCommand extends Command
{
    public final const PATH = '/local';

    public function __construct(
        private string $projectDir,
        private XMLAuditParser $parser,
        private AuditEngine $engine,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (scandir($this->projectDir . self::PATH) as $filename) {
            $path = $this->projectDir . self::PATH . '/' . $filename;
            if (!\is_file($path)) {
                continue;
            }
            $output->writeln("Processing {$filename}...");

            $time = new \DateTime();
            $xml = \simplexml_load_file($path, XMLElement::class);
            $entity = $this->parser->parse($xml);
            $timer = $time->diff(new \DateTime);
            $output->writeln("Parsed in {$timer->f} ms");
            $time = new \DateTime();

            $engine = ($this->engine)(input: $entity);
            $view = AuditView::from_engine($engine);
            dump($view->batiment);
            $timer = $time->diff(new \DateTime);
            $output->writeln("Simulation in {$timer->f} ms");

            $output->writeln("Done");
        }
        return Command::SUCCESS;
    }
}
