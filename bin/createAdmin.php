#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Nette\Database\Explorer;
use Nette\Security\Passwords;
use App\Model\Database\Values\Roles;
use App\Model\Database\Tables\Users;

$bootstrap = new Bootstrap();
$container = $bootstrap->bootWebApplication();

/** @var Explorer $database */
$database = $container->getByType(Explorer::class);

echo "Enter admin email: ";
$email = trim(fgets(STDIN));

echo "Enter admin username: ";
$username = trim(fgets(STDIN));

echo "Enter admin password: ";
$pass = trim(fgets(STDIN));

$existing = $database->table(Users::Table->value)->where(Users::Email->value, $email)->count();
if ($existing > 0) {
    echo "Error: user with this email already exists.\n";
    exit(1);
}
$existing = $database->table(Users::Table->value)->where(Users::Username->value, $username)->count();
if ($existing > 0) {
    echo "Error: user with this email already exists.\n";
    exit(1);
}

$hashedPassword = new Passwords()->hash($pass);

Nette\Utils\Validators::assert($email, 'email');

$user = $database
    ->table(Users::Table->value)
    ->insert([
        Users::Firstname->value => "Admin",
        Users::Lastname->value => "Admin",
        Users::Username->value => $username,
        Users::Email->value => $email,
        Users::Roles->value => Roles::Admin->value,
        Users::Password->value => $hashedPassword,
        Users::CreatedAt->value => new DateTime("now"),
    ]);

echo "Admin user created with ID: {$user->id}\n";
