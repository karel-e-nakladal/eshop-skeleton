<?php

declare(strict_types=1);

namespace App\Presentation\Accessory;

use App\Model\Database\Values\Roles;
use Nette\Security\Role;

trait RequiredAdmin
{
    public function injectRequireAdmin(): void
    {
		$this->onStartup[] = function () {
			$user = $this->getUser();

            $isAdmin = false;

            foreach($user->getRoles() as $role){
                switch($role){
                    case Roles::Editor:
                    case Roles::Moderator:
                    case Roles::Accountant:
                    case Roles::Inventory:
                    case Roles::Manager:
                    case Roles::Admin:
                        $isAdmin = true;
                        break;
                }
            }

			if (!$user->isLoggedIn()) {
				$this->flashMessage('You have to be logged in to access this site', 'info');
				$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
			} elseif ($isAdmin) {
                $this->flashMessage('You dont have permissions to access this site', 'error');
				$this->redirect(':App:Home:');
			}
		};
    }
}
