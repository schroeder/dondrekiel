<?php

namespace DondrekielAdminBundle\Command;

use DondrekielAppBundle\Entity\TeamLevelGame;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use DondrekielAdminBundle\Repository\MemberRepository;
use DondrekielAdminBundle\Repository\TeamLevelRepository;
use DondrekielAdminBundle\Repository\GameRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\TeamLevel;
use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Level;
use DondrekielAdminBundle\Game\GameLogic;
use FPDF;
use Endroid\QrCode\QrCode;

class CreatePlanBPdfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dondrekiel:buildplanb')
            ->setDescription('Build up plan b.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $output->writeln("<fg=blue>  Generate plan B PDF</fg=blue>");

        /* @var GameRepository $gameRepository */
        $gameRepository = $em->getRepository("DondrekielAdminBundle:Game");

        $gameList = $gameRepository->getAllUnassignedGames();

        $gameLogic = $this->getContainer()->get('dondrekiel.game.logic');

        $pdf = new FPDF();
        $fontSize = 12;
        $fontFamily = 'helvetica';
        $pdf->SetFont($fontFamily, '', $fontSize);

        /* @var Station $game */
        foreach ($gameList as $game) {

            if (!in_array($game->getGrade(), ['w', 'j', 'p', 'r', 'wj', 'pr', 'wjpr'])) {
                $output->writeln("<fg=red>  Game " . $game->getIdentifier() . "</fg=red>");
                continue;
            }
            $output->writeln("<fg=blue>  Game " . $game->getIdentifier() . "</fg=blue>");
            $pdf->AddPage();

            $grade = utf8_decode(GameLogic::getGradename($game->getGrade()));
            $location = $game->getLocation();
            $gameIdentifier = $game->getIdentifier();

            $pdf->SetFontSize(20);
            $pdf->Text(15, 20, utf8_decode("Level: " . $game->getLevel()->getNumber() . " // Stufe: " . $grade));
            $pdf->Line(15, 22, 200, 22);


            $pdf->SetFontSize(12);
            $pdf->Text(15, 110, utf8_decode("Euer Spiel heißt:"));
            $pdf->Text(15, 170, utf8_decode("Euer Spielort lautet:"));


            $pdf->SetFontSize(36);
            $pdf->Text(15, 122, utf8_decode($game->getName()));
            $pdf->Text(15, 142, utf8_decode($gameIdentifier));
            if ($location)
                $pdf->Text(15, 202, utf8_decode($location->getName()));

        }


        $pdf->Output('var/planb.pdf', 'F');

        $output->writeln("<fg=green>Done!</fg=green>");
    }

}