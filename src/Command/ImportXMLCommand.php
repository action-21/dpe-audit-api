<?php

namespace App\Command;

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
use App\Domain\Simulation\{SimulationFactory, SimulationService};
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
    public final const PATH = '/etc/audits';

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
        foreach (scandir($this->projectDir . self::PATH) as $filename) {
            $path = $this->projectDir . self::PATH . '/' . $filename;
            if (!\is_file($path)) {
                continue;
            }
            $output->writeln("Processing {$filename}...");

            $time = new \DateTime();
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
            $timer = $time->diff(new \DateTime);

            $output->writeln("Parsed in {$timer->f} ms");

            $time = new \DateTime();
            $this->simulation_service->calcule($simulation);
            $timer = $time->diff(new \DateTime);

            $output->writeln("Simulation in {$timer->f} ms");

            //dump($view->enveloppe->performance););
            //dump($simulation->audit()->occupation());
            //dump($simulation->chauffage()->besoins()->besoins(scenario: ScenarioUsage::CONVENTIONNEL));
            //dump($simulation->chauffage()->consommations()->consommations(scenario: ScenarioUsage::CONVENTIONNEL));
            //dump(\json_encode($view->chauffage));
            //dump($view->performances);
            //dump($audit->refroidissement()->besoin());
            //dump($audit->refroidissement()->consommation());
            //dump($view->occupation);
            //dump($view->enveloppe->apport);
            //dump($view->enveloppe->permeabilite);
            //dump($view->enveloppe->performance);
            //dump($view->enveloppe->murs);
            //dump($view->enveloppe->planchers_bas);
            //dump($view->enveloppe->planchers_hauts);
            //dump($view->enveloppe->baies);
            //dump($view->enveloppe->portes);
            //dump($view->enveloppe->locaux_non_chauffes);

            //$audit->controle();
            $output->writeln("Done");
        }
        return Command::SUCCESS;
    }
}
