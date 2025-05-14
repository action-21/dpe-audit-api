<?php

namespace App\Command;

use App\Api\Audit\ComputeAuditHandler;
use App\Database\Opendata\XMLElement;
use App\Serializer\Opendata\XMLAuditDeserializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:performance',
    description: 'Calcule de performance du moteur',
    hidden: false,
)]
final class PerformanceCommand extends Command
{
    public final const INPUT = '/data/audits';

    public function __construct(
        private readonly string $projectDir,
        private readonly XMLAuditDeserializer $deserializer,
        private readonly ComputeAuditHandler $handler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $counter = (int) $input->getArgument('counter');
        $filename = $input->getArgument('numero_dpe') . '.xml';
        $path = $this->projectDir . self::INPUT . '/' . $filename;

        if (!\is_file($path)) {
            return Command::FAILURE;
        }
        $xml = \simplexml_load_file($path, XMLElement::class);
        $audit = $this->deserializer->deserialize($xml);

        $time = new \DateTime();
        $output->writeln("Processing...");

        for ($i = 0; $i < $counter; $i++) {
            $handle = $this->handler;
            $audit = $handle($audit);
        }
        $timer = $time->diff(new \DateTime);
        $output->writeln("{$counter} simulations processed in {$timer->f} seconds");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::REQUIRED, 'Numéro de DPE :');
        $this->addArgument('counter', InputArgument::REQUIRED, 'Nombre d\'occurences :');
    }
}
