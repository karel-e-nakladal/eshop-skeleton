<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Mailer\MailService;
use App\Model\Database\Tables\Users;
use App\Model\Database\Tables\UserTokenes;
use App\Model\Database\Values\TokenType;
use Nette;
use Nette\Security\Passwords;
use Nette\Security\Authenticator;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;

final class SecurityFacade implements Nette\Security\Authenticator
{
	use Nette\SmartObject;

	public const PASSWORD_MIN_LENGTH = 7;


	public function __construct(
		private Nette\Database\Explorer $database, 
		private Passwords $passwords,
		private MailService $mailService,
	)
	{
	}
    
	public function authenticate(string $username, string $password): Nette\Security\SimpleIdentity
	{
		$row = $this->database->table(Users::Table->value)
			->where(Users::Username->value, $username)
			->where(Users::DeletedAt->value, null)
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

	public function add(string $username, string $email, string $password): int
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			$user = $this->database
			->table(Users::Table->value)
			->insert([
                Users::Username->value => $username,
				Users::Email->value => $email,
				Users::Password->value => $this->passwords->hash($password),
				Users::CreatedAt->value => new DateTime("now"),
			]);

			$verificationToken = bin2hex(random_bytes(32));

			$this->database->table(UserTokenes::Table->value)->insert([
				UserTokenes::UserId->value => $user->id,
				UserTokenes::TokenHash->value => hash('sha256', $verificationToken),
				UserTokenes::Type->value => TokenType::Verification->value,
				UserTokenes::CreatedAt->value => new DateTime("now"),
				UserTokenes::ExpiresAt->value => (new DateTime("now"))->modify('+1 day'),
			]);

			$cancelationToken = bin2hex(random_bytes(32));

			$this->database->table(UserTokenes::Table->value)->insert([
				UserTokenes::UserId->value => $user->id,
				UserTokenes::TokenHash->value => hash('sha256', $cancelationToken),
				UserTokenes::Type->value => TokenType::Cancelation->value,
				UserTokenes::CreatedAt->value => new DateTime("now"),
				UserTokenes::ExpiresAt->value => (new DateTime("now"))->modify('+1 day'),
			]);

			$this->mailService->sendWelcomeEmail($email, $username, $user->id, $verificationToken, $cancelationToken);
			
			return $user->id;
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	public function getUserById(int $id)
	{
		return $this->database->table(Users::Table->value)
			->get($id);
	}

	public function update(int $id, string $firstname, string $lastname, string $username, string $email, string $phonenumber, string $password)
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

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

	public function verifyEmail(int $id, string $token): void
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

		if( !$user) {
			throw new Nette\Security\AuthenticationException('User not found', 404);
		}

		if ($user[Users::VerifiedAt->value] !== null) {
			throw new Nette\Security\AuthenticationException('Email already verified', 403);
		}
		$now = new DateTime("now");
		$token = $this->database->table(UserTokenes::Table->value)
			->select('*')
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Verification->value)
			->where(UserTokenes::TokenHash->value, hash('sha256', $token))
			->fetch();

		if (!$token) {
			throw new Nette\Security\AuthenticationException('Invalid token', 403);
		}
		$user->update([
			Users::VerifiedAt->value => $now,
		]);
		$token->update([
			UserTokenes::UsedAt->value => $now,
		]);
		$this->database->table(UserTokenes::Table->value)
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Cancelation->value)
			->where(UserTokenes::DeletedAt->value, null)
			->where(UserTokenes::UsedAt->value, null)
			->update([UserTokenes::DeletedAt->value => $now]);
	}

	public function cancelEmailVerification(int $id, string $token): void
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

		if( !$user) {
			throw new Nette\Security\AuthenticationException('User not found', 404);
		}

		if ($user[Users::VerifiedAt->value] !== null) {
			throw new Nette\Security\AuthenticationException('Email already verified', 403);
		}

		$now = new DateTime("now");
		$token = $this->database->table(UserTokenes::Table->value)
			->select('*')
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Cancelation->value)
			->where(UserTokenes::TokenHash->value, hash('sha256', $token))
			->where(UserTokenes::ExpiresAt->value, ['>=', $now])
			->fetch();

		if (!$token) {
			throw new Nette\Security\AuthenticationException('Invalid token', 403);
		}
		$user->update([
			Users::DeletedAt->value => $now,
		]);
		$token->update([
			UserTokenes::UsedAt->value => $now,
		]);
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