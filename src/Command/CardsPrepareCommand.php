<?php

namespace App\Command;

use App\Entity\AnswerCard;
use App\Entity\CardFactory;
use App\Exception\BadCardTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CardsPrepareCommand extends Command
{
    protected static $defaultName = 'app:cards:prepare';
    private string $sourceDir;
    private string $destDir;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->sourceDir = dirname(__FILE__,3).'/files/';
        $this->destDir = dirname(__FILE__,3).'/public/media/';
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Card slasher')
            ->addArgument('type', InputArgument::REQUIRED, 'Card type (cards|memes)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');

        try {
            if ($type) {
                $io->note(sprintf('Importing type: %s', $type));
            }
            foreach(['questions', 'answers'] as $cardType){
                foreach (glob($this->sourceDir.$type.'/'.$cardType.'/*') as $cardFile) {
                    $io->note(sprintf('Processing file: %s', $cardFile));
                    $mediaFile = $cardType.'/'.basename($cardFile);
                    copy($cardFile, $this->destDir.$type.'/'.$mediaFile);
                    $card = CardFactory::create($cardType);
                    $card->setValue($mediaFile);
                    $card->setType($type);
                    $this->entityManager->persist($card);
                }
            }

            $this->entityManager->flush();
            $io->success('Done');
        } catch(BadCardTypeException $e){
            $io->error('Bad card type: '.$e->getMessage());
        }

        return 0;
    }
}
