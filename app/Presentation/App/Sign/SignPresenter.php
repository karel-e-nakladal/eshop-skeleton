<?php

declare(strict_types=1);

namespace App\Presentation\App\Sign;

use Nette;
use Nette\Application\UI\Form;
use Nette\Application\Attributes\Persistent;
use App\Model\Forms\SignInFormFactory;
use App\Model\Forms\SignUpFormFactory;
use App\Model\Database\Facades\SecurityFacade;
use App\Presentation\App\AppBasePresenter;

final class SignPresenter extends AppBasePresenter{


    #[Persistent]
    public string $backlink = '';

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
        if($this->user->isLoggedIn()){
            $this->flashMessage('You are already logged in', 'error');
            $this->redirect('Home:');
        }
    }
    public function createComponentSignInForm(): Form
    {
        $form = $this->signInFormFactory->create();
        $form->onSuccess[] = function (Nette\Forms\Form $form, \stdClass $values): void {
            try {
                $this->getUser()->login($values->username, $values->password);
                $this->flashMessage('You have been signed in.', 'success');
                $this->restoreRequest($this->backlink);
                $this->redirect('Home:');
            } catch (Nette\Security\AuthenticationException $e) {
                bdump($e->getMessage());
                $form->addError('Invalid email or password.');
            }
        };
        return $form;
    }
    
    public function renderUp(): void
    {
        if($this->user->isLoggedIn()){
            $this->flashMessage('You are already logged in', 'error');
            $this->redirect('Home:');
        }
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
                $this->restoreRequest($this->backlink);
                $this->redirect('Home:');
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError('Sign-up failed.');
            }
        };
        return $form;
    }

    public function actionOut(): void
    {
        if($this->user->isLoggedIn()){
            $this->getUser()->logout(true);
            $this->flashMessage('You have been signed out.', 'success');
            $this->redirect('Home:');
        }else{
            $this->flashMessage('You are not logged in', 'error');
            $this->redirect('Home:');
        }
    }
}
