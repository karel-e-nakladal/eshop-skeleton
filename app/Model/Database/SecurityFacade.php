<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Database\Values\Users;
use Nette;
use Nette\Security\Passwords;
use Nette\Security\Authenticator;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;

final class SecurityFacade implements Nette\Security\Authenticator
{
	use Nette\SmartObject;

	public const PASSWORD_MIN_LENGTH = 7;
	private Nette\Database\Explorer $database;

	private Passwords $passwords;


	public function __construct(Nette\Database\Explorer $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}
    
	public function authenticate(string $email, string $password): Nette\Security\SimpleIdentity
	{
		$row = $this->database->table(Users::Table->value)
			->where(Users::Email->value, $email)
			->fetch();


		if (!$row) {
			throw new Nette\Security\AuthenticationException('Nesprávné přihlačovací údaje [0]', Authenticator::IdentityNotFound);

		} elseif (!$this->passwords->verify($password, $row[Users::Password->value])) {
			throw new Nette\Security\AuthenticationException('Nesprávné přihlačovací údaje [1]', Authenticator::InvalidCredential);

		} elseif ($this->passwords->needsRehash($row[Users::Password->value])) {
			$row->update([
				Users::Password->value => $this->passwords->hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[Users::Password->value]);
		return new Nette\Security\SimpleIdentity($row[Users::Id->value], $row[Users::Roles->value], $arr);
	}

	public function add(string $firstname, string $lastname, string $username, string $email, string $phonenumber, string $password): int
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			$user = $this->database
			->table(Users::Table->value)
			->insert([
				Users::Firstname->value => $firstname,
                Users::Lastname->value => $lastname,
                Users::Username->value => $username,
				Users::Email->value => $email,
				Users::Phone->value => $phonenumber,
				Users::Password->value => $this->passwords->hash($password),
				Users::CreatedAt->value => new DateTime("now"),
			]);
			return $user->id;
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	public function getUserById(int $id)
	{
		return $this->database
			->table(Users::Table->value)
			->get($id);
	}

	public function update(int $id, string $firstname, string $lastname, string $username, string $email, string $phonenumber, string $password)
	{
		$user = $this->database
			->table(Users::Table->value)
			->get($id);

		$updateData = [
			Users::Firstname->value => $firstname,
			Users::Lastname->value => $lastname,
			Users::Username->value => $username,
			Users::Email->value => $email,
			Users::Phone->value => $phonenumber,
			Users::EditedAt->value => new DateTime("now")
		];
        
		if ($password !== null && trim($password) !== '') {
			$updateData[Users::Password->value] = $this->passwords->hash($password);
		}

		$user->update($updateData);

		return $user;
	}

    public function delete(int $id): void
    {
        $this->database->table(Users::Table->value)
            ->get($id)
            ->update([
                Users::DeletedAt->value => new DateTime("now")
            ]);
    }
}

class DuplicateNameException extends \Exception {
	
}