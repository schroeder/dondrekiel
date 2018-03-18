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

class CreateGamePdfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dondrekiel:buildgamepdf')
            ->setDescription('Build up the teams.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $output->writeln("<fg=blue>  Generate game PDF</fg=blue>");
        $temp_file = tempnam(sys_get_temp_dir(), 'packs_an_game_') . '.png';

        $logo_file = "src/DondrekielAdminBundle/Resources/public/images/logo_gross.png";

        /* @var GameRepository $gameRepository */
        $gameRepository = $em->getRepository("DondrekielAdminBundle:Game");

        $gameList = $gameRepository->findAll();

        $gameLogic = $this->getContainer()->get('dondrekiel.game.logic');

        $pdf = new FPDF();
        $fontSize = 12;
        $fontFamily = 'helvetica';
        $pdf->SetFont($fontFamily, '', $fontSize);

        /* @var Station $game */
        foreach ($gameList as $game) {
            if (!in_array($game->getGrade(), ['w', 'j', 'p', 'r', 'wj', 'pr', 'wjpr'])) {
                $output->writeln("<fg=red>  Team " . $game->getName() . "</fg=red>");
                continue;
            }
            $output->writeln("<fg=blue>  Team " . $game->getName() . "</fg=blue>");
            $pdf->AddPage();

            $line = "";
            $pos = 50;
            $group = "";
            $grade = "";
            $firstNames = [];
            /* @var Member $member */

            $grade = utf8_decode(GameLogic::getGradename($game->getGrade()));

            $jokerText = $this->getContainer()->get('templating')->render('DondrekielAdminBundle::register/game.txt.twig', ["game" => $game, "grade" => $grade]);

            $pdf->SetXY(20, 50);
            $pdf->SetFontSize(9);
            $pdf->MultiCell(180, 5, $jokerText, false, 'L');

            $qrCodeMessage = 'https://dondrekielspiel.isozaponol.de/login?qr=game:' . $game->getPasscode();

            /* @var QrCode $qrCode */
            $qrCode = new QrCode();
            $qrCode->setText($qrCodeMessage);

            $temp_file = tempnam(sys_get_temp_dir(), 'packs_an_game_') . '.png';
            $qrCode->writeFile($temp_file);
            $pdf->Image($temp_file, 130, 180, 60, 60);

            $location = $game->getLocation();
            $gameIdentifier = $game->getIdentifier();

            $pdf->SetFontSize(12);
            $pdf->Text(45, 200, utf8_decode("Dein Spiel heißt:"));
            $pdf->Text(45, 230, utf8_decode("Dein Spielort lautet:"));
            $pdf->Text(45, 260, utf8_decode("Das Lösungswort lautet:"));


            $pdf->Text(45, 207, utf8_decode($game->getName() . " (" . $gameIdentifier . ")"));
            $pdf->Text(45, 237, utf8_decode($location));
            $pdf->Text(45, 267, utf8_decode($game->getGameAnswer()));

            $pdf->Image($logo_file, 150, 10, 50, 50);
        }


        $pdf->Output('var/games.pdf', 'F');

        $output->writeln("<fg=green>Done!</fg=green>");
    }

}