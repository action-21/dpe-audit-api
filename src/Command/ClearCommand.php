<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:clear',
    description: 'Supprime les audits stockés localement',
    hidden: false,
)]
final class ClearCommand extends Command
{
    public final const PATH = '/etc/audits';

    public function __construct(
        private string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (scandir($this->projectDir . self::PATH) as $filename) {
            $file = $this->projectDir . self::PATH . '/' . $filename;
            if (\is_file($file)) {
                \unlink($file);
            }
        }
        return Command::SUCCESS;
    }
}
