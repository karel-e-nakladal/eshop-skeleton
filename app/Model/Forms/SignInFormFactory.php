<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

/**
 * Form factory for signing in the user
 */
final class SignInFormFactory{

    /**
     * username = Username;
     * password = Password;
     *
     * @return Form
     */
    public function create(): Form
    {
        $form = new Form;

        $form->addText('username', 'Username')
            ->setHtmlAttribute('placeholder', 'username')
            ->setRequired();

        $form->addPassword('password', 'Password')
            ->setHtmlAttribute('placeholder', '********')
            ->setRequired();

        $form->addSubmit('send', 'Login');

        return $form;
    }
}