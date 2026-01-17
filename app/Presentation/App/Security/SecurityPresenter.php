<?php

declare(strict_types=1);

namespace App\Presentation\App\Security;

use App\Model\Database\Facades\SecurityFacade;
use App\Presentation\App\AppBasePresenter;
use Nette;

final class SecurityPresenter extends AppBasePresenter{

    public function __construct(
        private SecurityFacade $securityFacade,
    ){
        return parent::__construct();
    }
    public function renderVerify(int $id, string $token): void{
        try{
            $this->securityFacade->verifyEmail($id, $token);
            $this->flashMessage('Email verification successful.', 'success');
            if($this->user->isLoggedIn()){
                $this->redirect('Home:');
            }else{
                $this->flashMessage('Please log in to continue', 'info');
                $this->redirect('Sign:in');
            }
        } catch (\Exception $e){
            $this->flashMessage('Email verification failed: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }

    public function renderRemember(int $id, string $token): void{
        try{
            $this->securityFacade->rememberAddress($id, $token);
            $this->flashMessage('This address will be remembered.', 'success');
            if($this->user->isLoggedIn()){
                $this->redirect('Home:');
            }else{
                $this->flashMessage('Please log in to continue', 'info');
                $this->redirect('Sign:in');
            }
        } catch (\Exception $e){
            $this->flashMessage('Email verification failed: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }

    public function renderCancel(int $id, string $token): void{
        try{
            $this->securityFacade->cancelEmailVerification($id, $token);
            $this->flashMessage('Account cancellation successful.', 'success');
            $this->redirect('Home:');
        } catch (\Exception $e){
            $this->flashMessage('Account cancellation failed: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }

    public function renderAuthenticate(int $id, string $token): void{
        try{
            $this->securityFacade->authenticateAddress($id, $token);
            $this->flashMessage('Authentication successful.', 'success');
            $this->redirect('Home:');
        } catch (\Exception $e){
            $this->flashMessage('Authentication failed: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }

    public function renderBlock(int $id, string $token): void{
        try{
            $this->securityFacade->blockAddress($id, $token);
            $this->flashMessage('Address blocked.', 'success');
            $this->redirect('Home:');
        } catch (\Exception $e){
            $this->flashMessage('Address could not be blocked: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }
}
