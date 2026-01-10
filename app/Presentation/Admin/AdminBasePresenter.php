<?php

declare(strict_types=1);

namespace App\Presentation\Admin;

use App\Presentation\Accessory\RequiredAdmin;
use Nette;


class AdminBasePresenter extends Nette\Application\UI\Presenter
{
    use RequiredAdmin;
}
