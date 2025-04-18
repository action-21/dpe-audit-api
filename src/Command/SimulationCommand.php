<?php

namespace App\Command;

use App\Api\Audit\ComputeAuditHandler;
use App\Api\Audit\Model\Audit as Resource;
use App\Database\Opendata\Audit\XMLAuditDeserializer;
use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Enveloppe\Enum\TypeDeperdition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:simulation',
    description: 'Simule les audits stockés localement au format XML',
    hidden: false,
)]
final class SimulationCommand extends Command
{
    public final const INPUT = '/data/audits';
    public final const OUTPUT = '/data/simulations';

    public array $rapport = [];

    public function __construct(
        private readonly string $projectDir,
        private readonly XMLAuditDeserializer $deserializer,
        private readonly ComputeAuditHandler $handler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $success = 0;
        $rapport = [];
        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') . '.xml' : null;

        foreach (scandir($this->projectDir . self::INPUT) as $filename) {
            $path = $this->projectDir . self::INPUT . '/' . $filename;
            if (!\is_file($path)) {
                continue;
            }
            if ($search && $filename !== $search) {
                continue;
            }
            $output->writeln("Processing {$filename}...");

            try {
                $xml = \simplexml_load_file($path, XMLElement::class);
                $audit = $this->deserializer->deserialize($xml);
                $success++;
            } catch (\Throwable $th) {
                if ($th->getCode() !== 400) {
                    $output->writeln("error file {$filename}");
                    throw $th;
                }
            }

            $this->save($audit);

            $handle = $this->handler;
            $audit = $handle($audit);
            $output->writeln("Etiquette énergie: {$audit->data()->etiquette_energie->id()}");
            $output->writeln("Etiquette climat: {$audit->data()->etiquette_climat->id()}");
            $output->writeln("Cef: {$audit->data()->consommations->get()}");
            $output->writeln("Cch: {$audit->chauffage()->data()->consommations->get()}");
            $output->writeln("Cecs: {$audit->ecs()->data()->consommations->get()}");
            $output->writeln("Cfr: {$audit->refroidissement()->data()->consommations->get()}");
            $output->writeln("Cecl: {$audit->eclairage()->data()->consommations->get()}");

            $output->writeln("Bch: {$audit->chauffage()->data()->besoins->get()}");
            $output->writeln("Becs: {$audit->ecs()->data()->besoins->get()}");

            $output->writeln("GV: {$audit->enveloppe()->data()->deperditions->get()}");
            $output->writeln("F: {$audit->enveloppe()->data()->apports->f()}");
            $output->writeln("Apports: {$audit->enveloppe()->data()->apports->apports()}");
            $output->writeln("Sse: {$audit->enveloppe()->data()->apports->sse()}");

            $this->addLine($xml, $audit, $rapport);
            $this->save($audit);
        }
        return Command::SUCCESS;
    }

    private function save(Audit $entity): void
    {
        $resource = Resource::from($entity);
        file_put_contents("{$this->projectDir}/data/simulations/{$entity->id()}.json", json_encode($resource, JSON_UNESCAPED_UNICODE));
    }

    private function addLine(XMLElement $xml, Audit $entity): void
    {
        $reader = XMLAuditReader::from($xml);

        $this->setLine('gv', $reader->enveloppe()->deperdition_enveloppe(), $entity->enveloppe()->data()->deperditions->get());
        $this->setLine('dp_murs', $reader->enveloppe()->deperdition_mur(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::MUR));
        $this->setLine('dp_planchers_bas', $reader->enveloppe()->deperdition_plancher_bas(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::PLANCHER_BAS));
        $this->setLine('dp_planchers_hauts', $reader->enveloppe()->deperdition_plancher_haut(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::PLANCHER_HAUT));
        $this->setLine('dp_baies', $reader->enveloppe()->deperdition_baie(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::BAIE));
        $this->setLine('dp_portes', $reader->enveloppe()->deperdition_porte(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::PORTE));
        $this->setLine('pt', $reader->enveloppe()->deperdition_pont_thermique(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::PONT_THERMIQUE));
        $this->setLine('dr', $reader->enveloppe()->deperdition_renouvellement_air(), $entity->enveloppe()->data()->deperditions->get(TypeDeperdition::RENOUVELEMENT_AIR));

        $this->setLine('hperm', $reader->enveloppe()->hperm(), $entity->enveloppe()->data()->permeabilite->hperm);
        $this->setLine('hvent', $reader->enveloppe()->hvent(), $entity->enveloppe()->data()->permeabilite->hvent);

        $this->setLine('besoin_ch', $reader->chauffage()->besoin_ch(), $entity->chauffage()->data()->besoins->get(ScenarioUsage::CONVENTIONNEL));
        $this->setLine('besoin_ch_depensier', $reader->chauffage()->besoin_ch(scenario_depensier: true), $entity->chauffage()->data()->besoins->get(ScenarioUsage::DEPENSIER));

        $this->setLine('besoin_ecs', $reader->ecs()->besoin_ecs(), $entity->ecs()->data()->besoins->get(ScenarioUsage::CONVENTIONNEL));
        $this->setLine('besoin_ecs_depensier', $reader->ecs()->besoin_ecs(scenario_depensier: true), $entity->ecs()->data()->besoins->get(ScenarioUsage::DEPENSIER));

        $this->setLine('besoin_fr', $reader->refroidissement()->besoin_fr(), $entity->refroidissement()->data()->besoins->get(ScenarioUsage::CONVENTIONNEL));
        $this->setLine('besoin_fr', $reader->refroidissement()->besoin_fr(scenario_depensier: true), $entity->refroidissement()->data()->besoins->get(ScenarioUsage::DEPENSIER));
    }

    private function setLine(string $name, float $origin, float $value): void
    {
        if (!isset($this->rapport[$name])) {
            $this->rapport[$name] = [];
        }
        $this->rapport[$name][] = [$origin, $value];
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE ?');
    }
}
