<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

/**
 * Form factory for changing the user password
 */
final class PasswordFormFactory{

    /**
     * CPassword = Current password;
     * NPassword = New password;
     * RPassword = Repeated new password;
     */
    public function create(): Form
    {
        $form = new Form;

        $form->addPassword('CPassword', 'Current password')
            ->setHtmlAttribute('placeholder', '********')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setRequired();
            
        $form->addPassword('NPassword', 'New password')
            ->setHtmlAttribute('placeholder', '********')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setRequired();
            
        $form->addPassword('RPassword', 'Repeat password')
            ->setHtmlAttribute('placeholder', '********')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setRequired();


        $form->addSubmit('send', 'Update');

        return $form;
    }
}