<?php

namespace DondrekielAdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DondrekielAppBundle\Entity\Joker;
use FPDF;
use Symfony\Component\HttpFoundation\Response;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Cookie;

class JokerGenerationController extends Controller
{
    /**
     * @Route("/Packs-An-Joker", name="requestajoker")
     */
    public function indexAction(Request $request)
    {
        // http://blog.michaelperrin.fr/2016/02/17/generating-pdf-files-with-symfony/
        $finalQuestion = false;
        $errorMessage = false;
        $response = new Response();

        if ($request->getMethod() == "POST") {
            if ($request->get('answer') && $request->get('answer') == "42") {

                $cookie = new Cookie('game-final-answer', md5('correct:' . time()), (time() + (3600 * 24 * 365)));
                $response->headers->setCookie($cookie);

                $session = $request->getSession();
                $session->set('game-final-answer', md5('correct:' . time()));
                $finalQuestion = true;
            } else {
                $errorMessage = "Die Antwort war jetzt nicht ganz richtig!";
            }
        } else {
            $cookies = $request->cookies;
            if ($cookies->has('game-final-answer') && $cookies->get('game-final-answer') != "") {
                $finalQuestion = true;
            }

        }
        if ($finalQuestion == true) {
            return $this->render('DondrekielAdminBundle::joker_generation/generate_joker.html.twig', [], $response);

        } else {
            return $this->render('DondrekielAdminBundle::joker_generation/index.html.twig', ['error_message' => $errorMessage], $response);
        }

    }

    /**
     * @Route("/getJoker", name="getajoker")
     */
    public function getJokerAction(Request $request)
    {
        $qrCodePasscode = md5(time() . '-' . rand(0, 100000));
        $qrCodeMessage = 'joker:' . $qrCodePasscode;

        $qrCode = new QrCode();
        $qrCode->setText($qrCodeMessage);

        $temp_file = tempnam(sys_get_temp_dir(), 'packs_an_joker_') . '.png';

        $logo_file = "../src/DondrekielAdminBundle/Resources/public/images/logo_gross.png";

        $qrCode->save($temp_file);

        $jokerText = $this->render('DondrekielAdminBundle::joker_generation/joker.txt.twig',
            array('qrcode_message' => $qrCodeMessage));

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(15, 20);
        $pdf->MultiCell(120, 5, $jokerText->getContent(), false, 'L');
        $pdf->Image($temp_file, 150, 150, 60, 60);
        $pdf->Image($logo_file, 150, 10, 50, 50);
        $pdf->Line(145, 150, 210, 150);
        $pdf->Line(145, 210, 210, 210);
        $pdf->SetFontSize(9);
        $pdf->Text(145, 149, 'Bitte hier knicken!');
        $pdf->Text(145, 213, 'Hier auch knicken!');

        $em = $this->getDoctrine()->getEntityManager();

        $joker = new Joker();
        $joker->setJokercode($qrCodePasscode);
        $joker->setJokerUsed(false);
        $joker->setCreated(time());
        $em->persist($joker);
        $em->flush();

        return new Response($pdf->Output(), 200, array(
            'Content-Type' => 'application/pdf'));
    }
}
