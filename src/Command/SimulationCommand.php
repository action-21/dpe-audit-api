<?php

namespace App\Command;

use App\Api\Simulation\Resource\SimulationResource;
use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\Chauffage\XMLChauffageTransformer;
use App\Database\Opendata\Eclairage\XMLEclairageTransformer;
use App\Database\Opendata\Ecs\XMLEcsTransformer;
use App\Database\Opendata\Enveloppe\XMLEnveloppeTransformer;
use App\Database\Opendata\Production\XMLProductionTransformer;
use App\Database\Opendata\Refroidissement\XMLRefroidissementTransformer;
use App\Database\Opendata\Ventilation\XMLVentilationTransformer;
use App\Database\Opendata\Visite\XMLVisiteTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Simulation\{Simulation, SimulationFactory, SimulationService};
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
    public final const PATH = '/etc/audits';
    public final const OUTPUT = '/var/output';

    public function __construct(
        private string $projectDir,
        private XMLAuditTransformer $xml_audit_transformer,
        private XMLEnveloppeTransformer $xml_enveloppe_transformer,
        private XMLChauffageTransformer $xml_chauffage_transformer,
        private XMLEcsTransformer $xml_ecs_transformer,
        private XMLRefroidissementTransformer $xml_refroidissement_transformer,
        private XMLVentilationTransformer $xml_ventilation_transformer,
        private XMLProductionTransformer $xml_production_transformer,
        private XMLEclairageTransformer $xml_eclairage_transformer,
        private XMLVisiteTransformer $xml_visite_transformer,
        private SimulationFactory $simulation_factory,
        private SimulationService $simulation_service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $counter = 0;
        $success = 0;
        $rapport = [];
        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') . '.xml' : null;
        $time = new \DateTime();

        foreach (scandir($this->projectDir . self::PATH) as $filename) {
            $path = $this->projectDir . self::PATH . '/' . $filename;
            if (!\is_file($path)) {
                continue;
            }
            if ($search && $filename !== $search) {
                continue;
            }
            $output->writeln("Processing {$filename}...");
            $counter++;

            try {
                $xml = \simplexml_load_file($path, XMLElement::class);
                $audit = $this->xml_audit_transformer->transform($xml);
                $enveloppe = $this->xml_enveloppe_transformer->transform($xml);
                $chauffage = $this->xml_chauffage_transformer->transform($xml);
                $ecs = $this->xml_ecs_transformer->transform($xml);
                $refroidissement = $this->xml_refroidissement_transformer->transform($xml);
                $ventilation = $this->xml_ventilation_transformer->transform($xml);
                $eclairage = $this->xml_eclairage_transformer->transform($xml);
                $production = $this->xml_production_transformer->transform($xml);
                $visite = $this->xml_visite_transformer->transform($xml);
                $success++;
            } catch (\Throwable $th) {
                if ($th->getCode() !== 400) {
                    $output->writeln("error file {$filename}");
                    throw $th;
                }
            }

            $simulation = $this->simulation_factory->build(
                audit: $audit,
                enveloppe: $enveloppe,
                chauffage: $chauffage,
                ecs: $ecs,
                refroidissement: $refroidissement,
                ventilation: $ventilation,
                eclairage: $eclairage,
                production: $production,
                visite: $visite,
            );
            $this->simulation_service->calcule($simulation);
            $view = SimulationResource::from($simulation);
            $this->setRapport($xml, $simulation, $rapport);
        }
        $timer = $time->diff(new \DateTime);
        $output->writeln("{$success}/{$counter} audits processed in {$timer->f} ms");
        $output = $this->projectDir . self::OUTPUT . '/rapport.json';
        file_put_contents($output, json_encode($rapport));

        return Command::SUCCESS;
    }

    protected function setRapport(XMLElement $xml, Simulation $simulation, array &$rapport): void
    {
        $rapport['gv'][] = [$xml->read_enveloppe()->deperdition_enveloppe(), $simulation->enveloppe()->performance()->gv];
        $rapport['dp_murs'][] = [$xml->read_enveloppe()->deperdition_mur(), $simulation->enveloppe()->parois()->murs()->dp()];
        $rapport['dp_pb'][] = [$xml->read_enveloppe()->deperdition_plancher_bas(), $simulation->enveloppe()->parois()->planchers_bas()->dp()];
        $rapport['dp_ph'][] = [$xml->read_enveloppe()->deperdition_plancher_haut(), $simulation->enveloppe()->parois()->planchers_hauts()->dp()];
        $rapport['dp_baies'][] = [$xml->read_enveloppe()->deperdition_baie(), $simulation->enveloppe()->parois()->baies()->dp()];
        $rapport['dp_portes'][] = [$xml->read_enveloppe()->deperdition_porte(), $simulation->enveloppe()->parois()->portes()->dp()];
        $rapport['pt'][] = [$xml->read_enveloppe()->deperdition_pont_thermique(), $simulation->enveloppe()->performance()->pt];
        $rapport['dr'][] = [$xml->read_enveloppe()->deperdition_renouvellement_air(), $simulation->enveloppe()->performance()->dr];
        $rapport['hperm'][] = [$xml->read_enveloppe()->hperm(), $simulation->enveloppe()->permeabilite()->hperm];
        $rapport['hvent'][] = [$xml->read_enveloppe()->hvent(), $simulation->enveloppe()->permeabilite()->hvent];

        $rapport['besoin_ch'][] = [$xml->read_chauffage()->besoin_ch(), $simulation->chauffage()->besoins()->besoins(scenario: ScenarioUsage::CONVENTIONNEL)];
        $rapport['besoin_ch_depensier'][] = [$xml->read_chauffage()->besoin_ch(scenario_depensier: true), $simulation->chauffage()->besoins()->besoins(scenario: ScenarioUsage::DEPENSIER)];

        $rapport['besoin_ecs'][] = [$xml->read_ecs()->besoin_ecs(), $simulation->ecs()->besoins()->besoins(scenario: ScenarioUsage::CONVENTIONNEL)];
        $rapport['besoin_ecs_depensier'][] = [$xml->read_ecs()->besoin_ecs(scenario_depensier: true), $simulation->ecs()->besoins()->besoins(scenario: ScenarioUsage::DEPENSIER)];

        $rapport['besoin_fr'][] = [$xml->read_refroidissement()->besoin_fr(), $simulation->refroidissement()->besoins()->besoins(scenario: ScenarioUsage::CONVENTIONNEL)];
        $rapport['besoin_fr_depensier'][] = [$xml->read_refroidissement()->besoin_fr(scenario_depensier: true), $simulation->refroidissement()->besoins()->besoins(scenario: ScenarioUsage::DEPENSIER)];
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE ?');
    }
}
