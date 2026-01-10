<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

final class SignInFormFactory{

    public function create(): Form
    {
        $form = new Form;

        $form->addEmail('username', 'Username')
            ->setHtmlAttribute('placeholder', 'username')
            ->setRequired();

        $form->addPassword('password', 'Password')
            ->setHtmlAttribute('placeholder', '********')
            ->setRequired();

        $form->addSubmit('send', 'Login');

        return $form;
    }
}