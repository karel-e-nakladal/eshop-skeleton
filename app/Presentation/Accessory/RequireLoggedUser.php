<?php

declare(strict_types=1);

namespace App\Presentation\Accessory;

trait RequireLoggedUser
{
    public function injectRequireLoggedUser(): void
    {
		$this->onStartup[] = function () {
			$user = $this->getUser();

			if ($user->isLoggedIn()) {
				return;
			} elseif ($user->getLogoutReason() === $user::LogoutInactivity) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.', 'info');
				$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
			} else {
				$this->flashMessage('You have to be logged in to access this site', 'info');
				$this->redirect('Sign:in');
			}
		};
    }
}
