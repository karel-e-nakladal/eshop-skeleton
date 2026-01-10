<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

final class SignUpFormFactory{

    public function create(): Form
    {
        $form = new Form;

        $form->addText('username', 'Username')
            ->setHtmlAttribute('placeholder', 'username')
            ->setRequired();

        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('placeholder', 'example@google.com')
            ->setRequired();
            
            $form->addPassword('password', 'Password')
            ->setHtmlAttribute('placeholder', '********')
            ->setRequired();

        $form->addSubmit('send', 'Sign Up');

        return $form;
    }
}