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
    private int $marginLeft = 40;
    private int $marginTop = 170;
    private int $cardWidth = 395;
    private int $cardHeight = 395;
    private int $cardsColumns = 4;
    private int $cardsRows = 5;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->sourceDir = dirname(__FILE__,3).'/files/';
        $this->destDir = dirname(__FILE__,3).'/public/media/cards/';
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Card slasher')
            ->addArgument('type', InputArgument::REQUIRED, 'Card type (white|black)')
            ->addArgument('limit', InputArgument::REQUIRED, 'Card limit (white=155|black=53)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $limit = $input->getArgument('limit');

        try {
            if ($type) {
                $io->note(sprintf('You passed an argument: %s', $type));
            }
            $cardIndex = 1;
            foreach (glob($this->sourceDir.$type.'*') as $cardsFile) {
                $io->note(sprintf('Processing file: %s', $cardsFile));

                $im = imagecreatefromjpeg($cardsFile);
                for($row = 0; $row < $this->cardsRows; $row++){
                    for($col = 0; $col < $this->cardsColumns; $col++){
                        $im2 = imagecrop($im, ['x' => $this->marginLeft + $col * $this->cardWidth, 'y' => $this->marginTop + $row * $this->cardHeight, 'width' => $this->cardWidth, 'height' => $this->cardHeight]);
                        if ($im2 !== false) {
                            if($cardIndex <= $limit){
                                $fileName = $type.str_pad($cardIndex++, 3, '0', STR_PAD_LEFT);
                                $io->note(sprintf('Saving file: %s', $fileName));
                                imagepng($im2, $this->destDir.$fileName.'.png');
                                $card = CardFactory::create($type);
                                $card->setValue($fileName);
                                $this->entityManager->persist($card);
                            }
                            imagedestroy($im2);
                        }
                    }
                }
                imagedestroy($im);
            }
            $this->entityManager->flush();
            $io->success('Done');
        } catch(BadCardTypeException $e){
            $io->error('Bad card type: '.$e->getMessage());
        }

        return 0;
    }
}
