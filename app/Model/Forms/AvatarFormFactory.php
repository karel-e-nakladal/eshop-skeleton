<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

/**
 * Form factory for changing the user avatar
 */
final class AvatarFormFactory{

    /**
     * avatar = Avatar;
     */
    public function create(): Form
    {
        $form = new Form;

        $form->addUpload('avatar', 'Avatar')
            ->setHtmlAttribute('placeholder', '********')
            ->setRequired();

        $form->addSubmit('send', 'Upload');

        return $form;
    }
}