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

    public function sendWelcomeEmail(string $email, string $username, int $id, string $verificationToken, string $cancelationToken): void {
        $verificationLink = $this->linkGenerator->link(':App:Profile:verify', ['id' => $id, 'token' => $verificationToken]);
        $cancelationLink = $this->linkGenerator->link(':App:Profile:cancel', ['id' => $id,'token' => $cancelationToken]);
        
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
}
