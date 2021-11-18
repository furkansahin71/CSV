<?php

namespace App\Command;

use App\Entity\Import;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:csv-import';

    public function __construct($projectDir, EntityManagerInterface $entityManagerInterface)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManagerInterface;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setDescription('importation du csv')
            ->setHelp('permet d\'importer le csv contact.csv')
            ->addArgument('csv', InputArgument::OPTIONAL,'nom du fichier csv', 'essai');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csv = $input->getArgument('csv');

        $personnes = $this->getCsvRowsAsArrays($csv);

        // $importation = $this->entityManager->getRepository(Import::class);
      
        foreach ($personnes as $personne) {
          
            $this->createUser($personne);
             
        }
        $this->entityManager->flush();

        $succes = new SymfonyStyle($input, $output);

        $succes->success('le csv a été importé');

        return Command::SUCCESS;
    }
    
    public function createUser($personne)
    {
        $user = new Import();

        $user->setNom($personne['nom']);
        $user->setPrenom($personne['prenom']);
        $user->setDateDeNaissance($personne['date_de_naissance']);
        $user->setAdresse($personne['adresse']);
        $user->setPays($personne['pays']);
        
        $this->entityManager->persist($user);
    }

    public function getCsvRowsAsArrays($csv)
    {
        $inputFile = $this->projectDir . '/public/data/' . $csv . '.csv';

        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($inputFile), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }
}
