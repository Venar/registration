<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 2:46 PM
 */

namespace AppBundle\Service\Util;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use AppBundle\Service\Repository\BadgeRepository;
use Symfony\Bridge\Twig\TwigEngine;

class Email
{
    /** @var $templating TwigEngine */
    protected $templating;
    /** @var $mailer \Swift_Mailer */
    protected $mailer;
    /** @var $badgeRepository BadgeRepository */
    protected $badgeRepository;

    public function __construct(TwigEngine $templating, \Swift_Mailer $mailer, BadgeRepository $badge)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->badgeRepository = $badge;
    }

    public function sendConfirmationEmail(Registration $registration)
    {
        $atcLogoIMG = 'http://registration.animedetour.com/images/atc_logo_small.png';
        $url = 'http://registration.animedetour.com/barcode/?text=$AD-C-' . urlencode($registration->getConfirmationnumber());

        $badges = $this->badgeRepository->getBadgesFromRegistration($registration);
        $isMinor = false;
        $isSponsor = false;
        foreach ($badges as $badge) {
            /* @var Badge $badge */
            $badgeType = $badge->getBadgetype()->getName();
            switch ($badgeType) {
                case 'MINOR':
                    $is_minor = true;
                    break;
                case 'ADREGSPONSOR':
                case 'ADREGCOMMSPONSOR':
                    $is_sponsor = true;
                    break;
                case 'GUEST':
                case 'VENDOR':
                case 'EXHIBITOR':
                case 'ADREGSTANDARD':
                case 'STAFF':
                    break;
            }
        }

        $options = [
            'registration' => $registration,
            'atcLogo' => $atcLogoIMG,
            'url' => $url,
            'isMinor' => $isMinor,
            'isSponsor' => $isSponsor,
        ];

        $message = \Swift_Message::newInstance()
            ->setSubject("Anime Detour {$registration->getEvent()->getYear()} Registration Confirmation")
            ->setFrom('ad_register@animedetour.com', 'Anime Detour Registration')
            ->setTo($registration->getEmail())
            ->setSender('ad_register@animedetour.com')
            ->setBody(
                $this->templating->render(
                    'email/confirmationemail.html.twig',
                    $options
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}