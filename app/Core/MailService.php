<?php

namespace App\Core\Mailer;

use Latte\Engine;
use Nette\Application\LinkGenerator;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

final class MailService
{

    private string $sender = 'infoluxuryqueen@seznam.cz';
    private string $name = 'No Reply';

    public function __construct(
        private Engine $latte,
        private LinkGenerator $linkGenerator,
        private Mailer $mailer,
    ) {
    }

    public function sendWelcome(string $email, string $username, int $id, string $verificationToken, string $cancelationToken): void {
        $verificationLink = $this->linkGenerator->link(':App:Security:verify', ['id' => $id, 'token' => $verificationToken]);
        $cancelationLink = $this->linkGenerator->link(':App:Security:cancel', ['id' => $id,'token' => $cancelationToken]);
        
        $html = $this->latte->renderToString(
            __DIR__ . '/MailTemplates/welcome.latte',
            [
                'username' => $username,
                'verificationLink' => $verificationLink,
                'cancelationLink' => $cancelationLink,
            ]
        );
            $mail = new Message;
            $mail->setFrom($this->sender, $this->name)
                ->addTo($email)
                ->setSubject('Welcome!')
                ->setHtmlBody($html);
    
            $this->mailer->send($mail);
    }

    public function sendLoginNotification(int $id, string $email, string $username, string $address, string $occurrence, string $blockToken): void {
        $blockLink = $this->linkGenerator->link(':App:Security:block', ['id' => $id,'token' => $blockToken]);

        $html = $this->latte->renderToString(
            __DIR__ . '/MailTemplates/loginNotification.latte',
            [
                'username' => $username,
                'address' => $address,
                'occurrence' => $occurrence,
                'blockLink' => $blockLink
            ]
        );
            $mail = new Message;
            $mail->setFrom($this->sender, $this->name)
                ->addTo($email)
                ->setSubject('New login!')
                ->setHtmlBody($html);
    
            $this->mailer->send($mail);
    }

    public function sendTwoFactor(int $id, string $email, string $username, string $address, string $occurrence, string $authenticationToken, string $blockToken): void {
        $authenticationLink = $this->linkGenerator->link(':App:Security:authenticate', ['id' => $id,'token' => $authenticationToken]);
        $blockLink = $this->linkGenerator->link(':App:Security:block', ['id' => $id,'token' => $blockToken]);

        $html = $this->latte->renderToString(
            __DIR__ . '/MailTemplates/twoFactor.latte',
            [
                'username' => $username,
                'address' => $address,
                'occurrence' => $occurrence,
                'blockLink' => $blockLink,
                'authenticationLink' => $authenticationLink
            ]
        );
            $mail = new Message;
            $mail->setFrom($this->sender, $this->name)
                ->addTo($email)
                ->setSubject('Two factor authentification')
                ->setHtmlBody($html);
    
            $this->mailer->send($mail);
    }
}
