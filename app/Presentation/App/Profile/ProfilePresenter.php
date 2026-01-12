<?php

declare(strict_types=1);

namespace App\Presentation\App\Profile;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Database\Facades\AddressFacade;
use App\Model\Database\Facades\SecurityFacade;
use App\Model\Forms\AddressFormFactory;
use App\Model\Database\Facades\UsersFacade;
use App\Model\Forms\AvatarFormFactory;
use App\Model\Forms\PasswordFormFactory;
use App\Presentation\Accessory\RequireLoggedUser;
use App\Presentation\App\AppBasePresenter;
use Nette\Security\SimpleIdentity;

final class ProfilePresenter extends AppBasePresenter{

    use RequireLoggedUser;

    public function __construct(
        private UsersFacade $usersFacade,
        private SecurityFacade $securityFacade,
        private AddressFormFactory $addressFormFactory,
        private AddressFacade $addressFacade,
        private PasswordFormFactory $passwordFormFactory,
        private AvatarFormFactory $avatarFormFactory,

    ){
        return parent::__construct();
    }
    
    public function renderDefault(): void{
    }


    public function createComponentAddAddress(): Form{
        $form = $this->addressFormFactory->create($this->user);

        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                $this->addressFacade->add(
                    $this->user->id, 
                    $data->firstname, 
                    $data->lastname,
                    $data->country,
                    $data->street,
                    $data->city,
                    $data->zip,
                    $data->hpone,
                    $data->email);
                $this->flashMessage('Address added successfully', 'success');
                $this->redirect("Profile:");
            } catch (\Exception $e) {
                $form->addError($e->getMessage());
            }
        };

        return $form;
    }

    public function createComponentUpdatePassword(): Form{
        $form = $this->passwordFormFactory->create();

        $form->onSuccess[] = function (Form $form, \stdClass $data): void{
            
            try{
                if(!$this->securityFacade->verifyPassword($this->user->getId(), $data->CPassword)){
                    $form->addError("Wrong password");
                }

                if($data->Npassword != $data->RPassword){
                    $form->addError("Passwords dont match");
                }

                $this->usersFacade->updatePassword($this->user->getId(), $data->NPassword);
                $this->flashMessage('Password changed successfully', 'success');
                $this->redirect('this');
            }catch(\Exception $e){
                $form->addError($e->getMessage());
            }
        };

        return $form;
    }

    public function createComponentUpdateAvatar(): Form{
        $form = $this->avatarFormFactory->create();

        $form->onSuccess[] = function (Form $form, \stdClass $data): void{
            try{
                if($data->avatar->isOk()){
                    $avatarPath = 'img/volatile/avatars/' . trim($this->user->getIdentity()->username) . "." . $data->avatar->getImageFileExtension();
                    $data->avatar->move($this->template->basePath . $avatarPath);
                }
                $this->usersFacade->updateAvatar($this->user->id, $avatarPath);
                $this->user->login(
                    new SimpleIdentity(
                        $this->user->getId(),
                        $this->user->getRoles(),
                        array_merge($this->user->getIdentity()->data, [
                            'avatar' => $avatarPath,
                        ])
                    )
                );
                $this->flashMessage("Avatar updated successfully", "success");
                $this->redirect('this');
            }catch(\Exception $e){
                $form->addError($e->getMessage());
            }
        };

        return $form;
    }

}
