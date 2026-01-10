<?php

declare(strict_types=1);

namespace App\Presentation\App\Sign;

use App\Model\Forms\SignInFormFactory;
use App\Model\Forms\SignUpFormFactory;
use App\Model\SecurityFacade;
use App\Presentation\App\AppBasePresenter;
use Nette;
use Nette\Application\UI\Form;

final class SignPresenter extends AppBasePresenter{

    public function __construct(
        private SignInFormFactory $signInFormFactory,
        private SignUpFormFactory $signUpFormFactory,
        private SecurityFacade $securityFacade,

    )
    {
        return parent::__construct();
    }

    public function renderIn(): void
    {
        $form = $this->signInFormFactory->create();
        $form->onSuccess[] = function (Nette\Forms\Form $form, \stdClass $values): void {
            try {
                $this->getUser()->login($values->email, $values->password);
                $this->flashMessage('You have been signed in.', 'success');
                $this->redirect('Home:');
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Invalid email or password.');
            }
        };
    }
    public function createComponentSignInForm(): Form
    {
        $form = $this->signInFormFactory->create();
        $form->onSuccess[] = function (Nette\Forms\Form $form, \stdClass $values): void {
            try {
                $this->getUser()->login($values->username, $values->password);
                $this->flashMessage('You have been signed in.', 'success');
                $this->redirect('Home:');
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Invalid email or password.');
            }
        };
        return $form;
    }
    
    public function renderUp(): void
    {
        
    }    

    public function createComponentSignUpForm(): Form
    {
        $form = $this->signUpFormFactory->create();
        $form->onSuccess[] = function (Nette\Forms\Form $form, \stdClass $values): void {
            try {
                $this->securityFacade->add(
                    $values->username,
                    $values->email,
                    $values->password
                );
                $this->getUser()->login($values->username, $values->password);
                $this->flashMessage('You have been signed up.', 'success');
                $this->redirect('Home:');
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Sign-up failed.');
            }
        };
        return $form;
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('You have been signed out.', 'success');
        $this->redirect('Home:');
    }
}
