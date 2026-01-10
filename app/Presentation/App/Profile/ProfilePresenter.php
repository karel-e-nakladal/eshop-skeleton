<?php

declare(strict_types=1);

namespace App\Presentation\App\Profile;

use App\Model\SecurityFacade;
use App\Presentation\App\AppBasePresenter;
use Nette;

final class ProfilePresenter extends AppBasePresenter{

    public function __construct(
        private SecurityFacade $securityFacade,
    ){
        return parent::__construct();
    }
    
    public function renderDefault(): void{
        
    }
    
    public function renderVerify(int $id, string $token): void{


        try{
            $this->securityFacade->verifyEmail($this->user->id, $token);
            $this->flashMessage('Email verification successful.', 'success');
            if($this->user->isLoggedIn()){
                $this->redirect('Home:');
            }else{
                $this->flashMessage('Please log in to continur', 'info');
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
}
