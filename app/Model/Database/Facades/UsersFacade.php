<?php

declare(strict_types=1);

namespace App\Model\Database\Facades;

use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;
use App\Model\Database\Exceptions\RowDeletedException;
use App\Model\Database\Exceptions\RowDoesntExistException;
use App\Model\Database\Tables\Users;

final class UsersFacade{

    use Nette\SmartObject;

	public function __construct(
		private Nette\Database\Explorer $database,
	)
	{
	}

	/**
	 * Get user by id
	 * Returns deleted users
	 *
	 * @param integer $id
	 * @throws RowDoesntExistException
	 * @return ActiveRow
	 */
	public function get(int $id): ActiveRow
	{
		$user = $this->database->table(Users::Table->value)
			->where(Users::Id->value, $id)
			->fetch();
		if(!$user){
			throw new RowDoesntExistException("User not found", 404);
		}

		return $user;
	}

    /**
	 * Updates the user information
	 *
	 * @param integer $id
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $email
	 * @param string $phonenumber
	 * @throws RowDoesntExistException
	 * @throws RowDeletedException
	 * @return void
	 */
	public function update(int $id, string $firstname, string $lastname, string $email, string $phonenumber)
	{
		$user = $this->database->table(Users::Table->value)
			->select('*')
			->where(Users::Id->value, $id)
			->fetch();

		if(!$user){
			throw new RowDoesntExistException("User not found", 404);
		}

		if($user[Users::DeletedAt->value] != null){
			throw new RowDeletedException("User was deleted");
		}
		
		$user->update([
			Users::Firstname->value => $firstname,
			Users::Lastname->value => $lastname,
			Users::Email->value => $email,
			Users::Phone->value => $phonenumber,
			Users::EditedAt->value => new DateTime()
		]);
	}

	/**
	 * Updates the user password
	 *
	 * @param integer $id
	 * @param string $password
	 * @throws RowDoesntExistException
	 * @throws RowDeletedException
	 * @return void
	 */
	public function updatePassword(int $id, string $assword){
		$user = $this->database->table(Users::Table->value)
			->where(Users::Id->value, $id)
			->fetch();

		if(!$user){
			throw new RowDoesntExistException("User not found", 404);
		}
		if($user[Users::DeletedAt->value] != null){
			throw new RowDeletedException("User was deleted");
		}

		$user->update([
			Users::Password->value => $this->passwords->hash($password),
			Users::EditedAt->value => new DateTime()
		]);
	}

    /**
     * Updates the user's avatar
     *
     * @param integer $id
     * @param string $path
     * @throws RowDoesntExistException
	 * @throws RowDeletedException
     * @return void
     */
    public function updateAvatar(int $id, string $path){
        $user = $this->database->table(Users::Table->value)
            ->where(Users::Id->value, $id)
            ->fetch();
        
        if(!$user){
			throw new RowDoesntExistException("User not found", 404);
		}
		if($user[Users::DeletedAt->value] != null){
			throw new RowDeletedException("User was deleted");
		}

        $user->update([
            Users::Avatar->value => $path,
            Users::EditedAt->value => new DateTime()
        ]);
    }

    /**
     * Soft deletes the user
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->database->table(Users::Table->value)
            ->get($id)
            ->update([
                Users::DeletedAt->value => new DateTime("now")
            ]);
    }
}