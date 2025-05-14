<?php

namespace App\Command;

use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;
use App\Database\Opendata\XMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:comparaison',
    description: 'Compare les résultats des audits simulés localement',
    hidden: false,
)]
final class ComparaisonCommand extends Command
{
    public final const INPUT = '/data/audits';
    public final const OUTPUT = '/data/simulations';

    private array $rapports = [];

    public function __construct(
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $readdir = $this->projectDir . self::INPUT;
        $datadir = $this->projectDir . self::OUTPUT;

        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') : null;

        if (false === $entries = scandir($readdir)) {
            $output->writeln("Le dossier source n'existe pas");
            return Command::FAILURE;
        }

        foreach ($entries as $filename) {
            $id = str_replace('.xml', '', $filename);
            $origin = "{$readdir}/{$id}.xml";
            $data = "{$datadir}/{$id}.json";

            if (!\is_file($origin) || !\is_file($data)) {
                continue;
            }
            if ($search && $id !== $search) {
                continue;
            }

            $xml = \simplexml_load_file($origin, XMLElement::class);
            $json = \json_decode(file_get_contents($data), true);

            $this->addRapport($xml, $json);
        }
        $this->build($output);
        return Command::SUCCESS;
    }

    public function addRapport(XMLElement $xml, array $data): void
    {
        $reader = XMLEnveloppeReader::from($xml);
        $this->addItem('gv', $reader->gv(), $data['enveloppe']['data']['gv']);
        $this->addItem('dp', $reader->dp(), $data['enveloppe']['data']['dp']);
        $this->addItem('pt', $reader->pt(), $data['enveloppe']['data']['dr']);
        $this->addItem('dr', $reader->dr(), $data['enveloppe']['data']['dr']);
        $this->addItem('hperm', $reader->hperm(), $data['enveloppe']['data']['permeabilite']['hperm']);
        $this->addItem('hvent', $reader->hvent(), $data['enveloppe']['data']['permeabilite']['hvent']);
    }

    public function addItem(string $key, float $origin, float $value): void
    {
        if (!isset($this->rapports[$key])) {
            $this->rapports[$key] = [];
        }
        $this->rapports[$key][] = [$origin, $value];
    }

    public function build(OutputInterface $output): void
    {
        $rapport = [];

        foreach ($this->rapports as $key => $values) {
            $ecarts = [];
            foreach ($values as $value) {
                $ecarts[] = ($value[1] - $value[0]) / $value[0];
            }
            $rapport[$key] = array_sum($ecarts) / count($ecarts);
        }

        foreach ($rapport as $key => $value) {
            $ecart = round($value * 100, 2);
            $output->writeln("{$key} : {$ecart}%");
        }
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE :');
    }
}
