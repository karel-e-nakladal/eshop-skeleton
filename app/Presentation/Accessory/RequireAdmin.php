<?php

declare(strict_types=1);

namespace App\Presentation\Accessory;

use Nette\Security\Role;

trait RequiredAdmin
{
    public function InjectRequireAdmin(): void
    {
        $this->onStartup[] = function (): void {
            $user = $this->getUser();
        };
    }
}
