<?php

declare(strict_types=1);

namespace App\Model\Database\Facades;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\Authenticator;
use Nette\Utils\DateTime;
use App\Core\Mailer\MailService;
use App\Model\Database\Exceptions\AddressNotVerified;
use App\Model\Database\Exceptions\DuplicateException;
use App\Model\Database\Exceptions\RowDoesntExistException;
use App\Model\Database\Tables\AccessTokens;
use App\Model\Database\Tables\UserAccesses;
use App\Model\Database\Tables\Users;
use App\Model\Database\Tables\UserTokenes;
use App\Model\Database\Values\RememberAccesses;
use App\Model\Database\Values\TokenType;
use App\Model\Database\Values\UserTwoFactor;
use Nette\Http\Request;

final class SecurityFacade implements Nette\Security\Authenticator
{
	use Nette\SmartObject;

	public const PASSWORD_MIN_LENGTH = 7;


	public function __construct(
		private Nette\Database\Explorer $database, 
		private Passwords $passwords,
		private MailService $mailService,
		private Request $httpRequest,
	)
	{
	}
    
	/**
	 * Authenticates the user
	 *
	 * @param string $username
	 * @param string $password
	 * @throws AuthenticationException
	 * @return Nette\Security\SimpleIdentity
	 */
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

		// Checking address of connection
		$address = $this->httpRequest->getRemoteAddress();

		$now = new DateTime();
		$tokenExpiration = new DateTime()->modify("+7 day");

		// Checking if user has 2FA enabled
		if($row[Users::TwoFactor->value] == UserTwoFactor::True->value){
			
			$authCheck = $this->database->table(UserAccesses::Table->value)
			->where(UserAccesses::UserId->value, $row[Users::Id->value])
			->where(UserAccesses::From->value, $address)
			->where(UserAccesses::Remember->value, RememberAccesses::True->value,)
			->fetch();
			
			if(!$authCheck){
				// Creating access record
				$access = $this->database->table(UserAccesses::Table->value)
				->insert([
					UserAccesses::UserId->value => $row[Users::Id->value],
					UserAccesses::From->value => $address,
					UserAccesses::CreatedAt->value => $now
				]);
				
				// Creating address blocking token
				$blockToken = bin2hex(random_bytes(32));
				$token = $this->database->table(UserTokenes::Table->value)->insert([
					UserTokenes::UserId->value => $row[Users::Id->value],
					UserTokenes::TokenHash->value => hash('sha256', $blockToken),
					UserTokenes::Type->value => TokenType::AddressBlock->value,
					UserTokenes::CreatedAt->value => $now,
					UserTokenes::ExpiresAt->value => $tokenExpiration,
				]);
				
				// Connecting token and access record
				$this->database->table(AccessTokens::Table->value)
				->insert([
					AccessTokens::UserAccess->value => $access[UserAccesses::Id->value],
					AccessTokens::UserToken->value => $token[UserTokenes::Id->value]
				]);
				
				// Creating 2FA token
				$authenticationToken = bin2hex(random_bytes(32));
				$authenticationTokenExpiration = new DateTime()->modify("+1 hour");
				$twoFactorToken = $this->database->table(UserTokenes::Table->value)->insert([
					UserTokenes::UserId->value => $row[Users::Id->value],
					UserTokenes::TokenHash->value => hash('sha256', $authenticationToken),
					UserTokenes::Type->value => TokenType::TwoFactor->value,
					UserTokenes::CreatedAt->value => $now,
					UserTokenes::ExpiresAt->value => $authenticationTokenExpiration,
				]);

				// Connecting token and access record
				$this->database->table(AccessTokens::Table->value)
					->insert([
						AccessTokens::UserAccess->value => $access[UserAccesses::Id->value],
						AccessTokens::UserToken->value => $twoFactorToken[UserTokenes::Id->value]
					]);
	
				// Sending an email with 2FA token and information about login
				$this->mailService->sendTwoFactor($row[Users::Id->value], $row[Users::Email->value], $row[Users::Username->value], $address, $now->__toString(), $authenticationToken, $blockToken);
				throw new AddressNotVerified("You have logged in from new address, please check your email for more information");
			}
		}else{
			
			$check = $this->database->table(UserAccesses::Table->value)
				->where(UserAccesses::UserId->value, $row[Users::Id->value])
				->where(UserAccesses::From->value, $address)
				->where(UserAccesses::CreatedAt->value . ' >= ? ', new DateTime()->modify("-7 days"))
				->fetch();

			if(!$check){
			
				// Creating access record
				$access = $this->database->table(UserAccesses::Table->value)
				->insert([
					UserAccesses::UserId->value => $row[Users::Id->value],
					UserAccesses::From->value => $address,
					UserAccesses::CreatedAt->value => $now
				]);
				
				// Creating address blocking token
				$blockToken = bin2hex(random_bytes(32));
				$token = $this->database->table(UserTokenes::Table->value)->insert([
					UserTokenes::UserId->value => $row[Users::Id->value],
					UserTokenes::TokenHash->value => hash('sha256', $blockToken),
					UserTokenes::Type->value => TokenType::AddressBlock->value,
					UserTokenes::CreatedAt->value => $now,
					UserTokenes::ExpiresAt->value => $tokenExpiration,
				]);
				
				// Connecting token and access record
				$this->database->table(AccessTokens::Table->value)
					->insert([
						AccessTokens::UserAccess->value => $access[UserAccesses::Id->value],
						AccessTokens::UserToken->value => $token[UserTokenes::Id->value]
					]);

				// Sending an email with information about login
				$this->mailService->sendLoginNotification($row[Users::Id->value], $row[Users::Email->value], $row[Users::Username->value], $address, $now->__toString(), $blockToken);
			}
		}

		$arr = $row->toArray();
		unset($arr[Users::Password->value]);
		return new Nette\Security\SimpleIdentity($row[Users::Id->value], $row[Users::Roles->value], $arr);
	}

	/**
	 * Creates a new user and tokens for verification and cancelation
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @throws DuplicateException
	 * @return integer
	 */
	public function add(string $username, string $email, string $password): int
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			// Verifiing email availability
			$check = $this->database->table(Users::Table->value)
				->where(Users::Email->value, $email)
				->where(Users::DeletedAt->value, null)
				->fetch();

			if($check){
				throw new DuplicateException("User with this email already exist");
			}

			// Setting up times
			$now = new DateTime();
			$tokenExpiration = $now->modify("+1 day");

			// Creating user
			$user = $this->database
			->table(Users::Table->value)
			->insert([
                Users::Username->value => $username,
				Users::Email->value => $email,
				Users::Password->value => $this->passwords->hash($password),
				Users::CreatedAt->value => $now,
			]);

			// Creating email verificaion token
			$verificationToken = bin2hex(random_bytes(32));
			$this->database->table(UserTokenes::Table->value)->insert([
				UserTokenes::UserId->value => $user->id,
				UserTokenes::TokenHash->value => hash('sha256', $verificationToken),
				UserTokenes::Type->value => TokenType::Verification->value,
				UserTokenes::CreatedAt->value => $now,
				UserTokenes::ExpiresAt->value => $tokenExpiration,
			]);

			// Creates email cancelation token
			$cancelationToken = bin2hex(random_bytes(32));
			$this->database->table(UserTokenes::Table->value)->insert([
				UserTokenes::UserId->value => $user->id,
				UserTokenes::TokenHash->value => hash('sha256', $cancelationToken),
				UserTokenes::Type->value => TokenType::Cancelation->value,
				UserTokenes::CreatedAt->value => $now,
				UserTokenes::ExpiresAt->value => $tokenExpiration->modify("+6 day"),
			]);

			// Sends welcome mail
			$this->mailService->sendWelcome($email, $username, $user->id, $verificationToken, $cancelationToken);
			
			return $user->id;
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateException();
		}
	}

	/**
	 * Compares password to hash from database
	 *
	 * @param integer $id
	 * @param string $password
	 * @throws RowDoesntExistException
	 * @throws AuthenticationException
	 * @return boolean
	 */
	public function verifyPassword(int $id, string $password): bool{
		$user = $this->database->table(Users::Table->value)
			->where(Users::Id->value, $id)
			->fetch();

		if( !$user) {
			throw new RowDoesntExistException("User not found", 404);
		}

		if ($user[Users::VerifiedAt->value] !== null) {
			throw new Nette\Security\AuthenticationException('Email already verified', 403);
		}

		return $this->passwords->verify($password, $user[Users::Password->value]);
	}

	/**
	 * Verifies email
	 *
	 * @param integer $id
	 * @param string $token
	 * @throws RowDoesntExistException
	 * @throws AuthenticationException
	 * @return void
	 */
	public function verifyEmail(int $id, string $token): void
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

		if( !$user) {
			throw new RowDoesntExistException("User not found", 404);
		}

		if ($user[Users::VerifiedAt->value] !== null) {
			throw new Nette\Security\AuthenticationException('Email already verified', 403);
		}

		$now = new DateTime();
		$token = $this->database->table(UserTokenes::Table->value)
			->select('*')
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Verification->value)
			->where(UserTokenes::TokenHash->value, hash('sha256', $token))
			->fetch();

		if (!$token) {
			throw new Nette\Security\AuthenticationException('Invalid token', 403);
		}
		// Deleting tokens and verifiing user
		$user->update([
			Users::VerifiedAt->value => $now,
		]);
		$token->update([
			UserTokenes::UsedAt->value => $now,
		]);
		$this->database->table(UserTokenes::Table->value)
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Cancelation->value)
			->where(UserTokenes::UsedAt->value, null)
			->where(UserTokenes::DeletedAt->value, null)
			->update([UserTokenes::DeletedAt->value => $now]);
	}

	public function authenticateAddress(int $id, string $token){
	/*	$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

		if( !$user) {
			throw new RowDoesntExistException("User not found", 404);
		}
		$token = $this->database->table(UserTokenes::Table->value)
			->where(UserTokenes::TokenHash->value, hash('sha256', $token))
			->fetch();

		if () {
			throw new Nette\Security\AuthenticationException('Address already authenticated', 403);
		}

		$now = new DateTime();
		$token = $this->database->table(UserTokenes::Table->value)
			->select('*')
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Verification->value)
			->where(UserTokenes::TokenHash->value, hash('sha256', $token))
			->fetch();

		if (!$token) {
			throw new Nette\Security\AuthenticationException('Invalid token', 403);
		}
		// Deleting tokens and verifiing user
		$user->update([
			Users::VerifiedAt->value => $now,
		]);
		$token->update([
			UserTokenes::UsedAt->value => $now,
		]);
		$this->database->table(UserTokenes::Table->value)
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Cancelation->value)
			->where(UserTokenes::UsedAt->value, null)
			->where(UserTokenes::DeletedAt->value, null)
			->update([UserTokenes::DeletedAt->value => $now]);*/
	}

	public function blockAddress(int $id, string $token){
		
	}

	/**
	 * 
	 *
	 * @param integer $id
	 * @param string $token
	 * @return void
	 */
	public function cancelEmailVerification(int $id, string $token): void
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->where(Users::DeletedAt->value, null)
			->fetch();

		if( !$user) {
			throw new RowDoesntExistException("User not found", 404);
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

		// Deleting tokens and user
		$user->update([
			Users::DeletedAt->value => $now,
		]);
		$token->update([
			UserTokenes::UsedAt->value => $now,
		]);
		$this->database->table(UserTokenes::Table->value)
			->where(UserTokenes::UserId->value, $id)
			->where(UserTokenes::Type->value, TokenType::Verification->value)
			->where(UserTokenes::UsedAt->value, null)
			->where(UserTokenes::DeletedAt->value, null)
			->update([UserTokenes::DeletedAt->value => $now]);
	}
}
