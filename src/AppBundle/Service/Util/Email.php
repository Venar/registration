<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Service\Util;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\TwigEngine;

class Email
{
    /** @var $templating TwigEngine */
    protected $templating;
    /** @var $mailer \Swift_Mailer */
    protected $mailer;
    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(TwigEngine $templating, \Swift_Mailer $mailer, EntityManager $entityManager)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Registration $registration
     * @param bool $forceResend Resend even if already set
     */
    public function generateAndSendConfirmationEmail(Registration $registration, bool $forceResend = false)
    {
        if ($registration->getEmail() == ''
            || ($registration->getConfirmationnumber() != ''
                && !$forceResend
            )
        ) {

            return;
        }

        if ($registration->getConfirmationnumber() == '') {
            $this
                ->entityManager
                ->getRepository(Registration::class)
                ->generateConfirmationNumber($registration);
        }

        try {
            $this->sendConfirmationEmail($registration);
        } catch (\Exception $e) {
            $this->sendErrorMessageToRegistration($e->getMessage(), $registration);
        }
    }

    private function sendConfirmationEmail(Registration $registration)
    {
        $atcLogoIMG = 'http://registration.animedetour.com/images/atc_logo_small.png';
        $url = 'http://registration.animedetour.com/api/barcode/$AD-C-' . urlencode($registration->getConfirmationnumber());

        $badges = $registration->getBadges();
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

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject("Anime Detour {$registration->getEvent()->getYear()} Registration Confirmation")
                ->setFrom('noreply@animedetour.com', 'Anime Detour IT')
                ->setReplyTo('ad_register@animedetour.com', 'Anime Detour Registration')
                ->setTo($registration->getEmail())
                ->setSender('noreply@animedetour.com')
                ->setBody(
                    $this->templating->render(
                        'email/confirmationemail.html.twig',
                        $options
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $error
     * @param Registration|null $registration
     */
    public function sendErrorMessageToRegistration($error, Registration $registration = null) {
        $options = [
            'registration' => $registration,
            'error' => $error,
        ];

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject("Registration Ingest Error")
                ->setFrom('noreply@animedetour.com', 'Anime Detour IT')
                ->setReplyTo('ad_register@animedetour.com', 'Anime Detour Registration')
                ->setTo(['ad_register@animedetour.com', 'it@animedetour.com'])
                ->setSender('noreply@animedetour.com')
                ->setBody(
                    $this->templating->render(
                        'email/emailError.html.twig',
                        $options
                    ),
                    'text/html'
                )
            ;
            $this->mailer->send($message);
        } catch (\Exception $e) {

        }
    }
}