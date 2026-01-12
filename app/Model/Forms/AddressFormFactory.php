<?php

declare(strict_types=1);
namespace App\Model\Forms;

use Nette\Application\UI\Form;

/**
 * Form factory for adding addresses
 */
final class AddressFormFactory{

    /**
     * firstname = First name;
     * lastname = Last name;
     * country = Country;
     * city = City;
     * street = Street;
     * zip = ZIP code;
     * phone = Phone number;
     * email = Email;
     *
     * @param [type] $user
     * @return Form
     */
    public function create($user): Form
    {
        bdump($user->getIdentity());
        if(isset($user)){
            $identity = $user->getIdentity()->data;
        }
        $form = new Form;

        $form->addText('firstname', 'First name')
            ->setDefaultValue(isset($user) ? $identity["firstname"] : "")
            ->setHtmlAttribute('placeholder', 'John')
            ->setRequired();
            
        $form->addText('lastname', 'Last name')
            ->setDefaultValue(isset($user) ? $identity["lastname"] : "")
            ->setHtmlAttribute('placeholder', 'Doe')
            ->setRequired();

        $form->addText('country', 'Country')
            ->setDefaultValue("Czech Republic")
            ->setHtmlAttribute('placeholder', 'Czechia')
            ->setRequired();

        $form->addText('city', 'City')
            ->setDefaultValue("")
            ->setHtmlAttribute('placeholder', 'Kolín')
            ->setRequired();
            
        $form->addText('street', 'Street and number')
            ->setDefaultValue("")
            ->setHtmlAttribute('placeholder', 'Pražská 69')
            ->setRequired();

        $form->addText('zip', 'ZIP code')
            ->setDefaultValue("")
            ->setHtmlAttribute('placeholder', '420 69')
            ->setRequired();
        
        $form->addText('phone', 'Phone number')
            ->setDefaultValue(isset($user) ? $identity["phone"] : "+420")
            ->setHtmlAttribute('placeholder', '+420 123456789')
            ->setRequired();
        
        $form->addText('email', 'Email')
            ->setDefaultValue(isset($user) ? $identity["email"] : "")
            ->setHtmlAttribute('placeholder', '420 69')
            ->setRequired();

        $form->addSubmit('send', 'Login');

        return $form;
    }
}