<?php
namespace EducacityREST\SiteBundle\Command;

use EducacityREST\SiteBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportSitesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:sites')
            ->setDescription('Creates a list of sites');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $path = __DIR__ . '/../DataFixtures/sites.csv';

        $handler = fopen($path, "r");

        while ($data = fgetcsv($handler, 1000, ',')) {
            $site = new Site();
            $site->setName($data[0]);
            $site->setLatitude($data[1]);
            $site->setLongitude($data[2]);
            $site->setInformation($data[3]);
            $em->persist($site);
        }
        $em->flush();
    }
}
