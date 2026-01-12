<?php

declare(strict_types=1);

namespace App\Model\Database\Facades;

use Nette;
use Nette\Database\Table\ActiveRow;
use App\Model\Database\Exceptions\RowDeletedException;
use App\Model\Database\Exceptions\RowDoesntExistException;
use App\Model\Database\Exceptions\RowDoestnExistException;
use App\Model\Database\Tables\Addresses;
use App\Model\Database\Tables\Orders;
use Nette\Utils\DateTime;

final class AddressFacade
{
	use Nette\SmartObject;

	public function __construct(
		private Nette\Database\Explorer $database,
	)
	{
	}

    /**
     * Returns address even if set as deleted
     *
     * @param integer $id
     * @throws RowDoesntExistEsception
     * @return void
     */
    public function get(int $id){
        $data = $this->database->table(Addresses::Table->value)
            ->get($id);

        if(!$data){
            throw new RowDoesntExistException("Address not found", 404);
        }
        return $data;
    }

    /**
     * Adds a new address to user
     *
     * @param integer $userId
     * @param string $firstname
     * @param string $lastname
     * @param string $country
     * @param string $street
     * @param string $city
     * @param string $zip
     * @param string $phone
     * @param string $email
     * @return void
     */
    public function add(
        int $userId, 
        string $firstname, 
        string $lastname, 
        string $country, 
        string $street, 
        string $city, 
        string $zip, 
        string $phone, 
        string $email
    ): void
    {
        $this->database->table(Addresses::Table->value)
            ->insert([
                Addresses::UserId->value => $userId,
                Addresses::FirstName->value => $firstname,
                Addresses::LastName->value => $lastname,
                Addresses::Country->value => $country,
                Addresses::Street->value => $street,
                Addresses::City->value => $city,
                Addresses::ZipCode->value => $zip,
                Addresses::Phone->value => $phone,
                Addresses::Email->value => $email,
                Addresses::CreatedAt->value => new DateTime()
            ]);
    }

    /**
     * Returns an ActiveRow array of addresses associated iwth user
     *
     * @param integer $userId
     * @throws RowDoesntExistException
     * @return array
     */
    public function getByUserId(
        int $userId
    ): array
    {
        $data = $this->database->table(Addresses::Table->value)
            ->select('*')
            ->where(Addresses::UserId->value, $userId)
            ->where(Addresses::DeletedAt->value, null)
            ->fetchAll();
        
        if(count($data) == 0){
            throw new RowDoesntExistException("Addresses not found", 404);
        }

        return $data;
    }

    /**
     * Updates address
     * 
     * If the address is used it will create new record (with same creation date) to preserve order details
     *
     * @param integer $id
     * @param string $firstname
     * @param string $lastname
     * @param string $country
     * @param string $street
     * @param string $city
     * @param string $zip
     * @param string $phone
     * @param string $email
     * @throws RowDoesntExistException
     * @throws RowDeletedException
     * @return void
     */
    public function update(
        int $id,
        string $firstname,
        string $lastname,
        string $country,
        string $street,
        string $city,
        string $zip,
        string $phone,
        string $email
    )
    {

        $address = $this->database->table(Addresses::Table->value)
            ->where(Addresses::Id->value, $id)
            ->fetch();

        // Check if address exists
        if(!$address){
            throw new RowDoesntExistException("Address not found", 404);
        }

        // Check if address is not deleted
        if($address[Addresses::DeletedAt->value] != null){
            throw new RowDeletedException("Address was deleted");
        }

        // Get address usage
        $usage = $this->database->table(Orders::Table->value)
            ->where(Orders::AddressId->value, $id)
            ->fetchAll();

        $now = new DateTime();

        if(count($usage) > 0){
            // crate new address if the address is used to preserve data usage
            $this->database->table(Addresses::Table->value)
                ->insert([
                Addresses::UserId->value => $address[Addresses::UserId->value],
                Addresses::FirstName->value => $firstname,
                Addresses::LastName->value => $lastname,
                Addresses::Country->value => $country,
                Addresses::Street->value => $street,
                Addresses::City->value => $city,
                Addresses::ZipCode->value => $zip,
                Addresses::Phone->value => $phone,
                Addresses::Email->value => $email,
                Addresses::CreatedAt->value => $address[Addresses::CreatedAt->value],
                Addresses::EditedAt->value => $now
            ]);
            $address->update([
                Addresses::DeletedAt->value => $now
            ]);
        }else{
            // Update adress if it is not used
            $address->update([
                Addresses::FirstName->value => $firstname,
                Addresses::LastName->value => $lastname,
                Addresses::Country->value => $country,
                Addresses::Street->value => $street,
                Addresses::City->value => $city,
                Addresses::ZipCode->value => $zip,
                Addresses::Phone->value => $phone,
                Addresses::Email->value => $email,
                Addresses::EditedAt->value => $now
            ]);
        }
    }

    /**
     * DSoft dletes address
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $data = $this->database->table(Addresses::Table->value)
            ->where(Addresses::Id->value, $id)
            ->fetch();
        
        if(!$data){
            throw new RowDoesntExistException("Address not found", 404);
        }

        if($data[Addresses::DeletedAt->value] != null){
            throw new RowDeletedException("Address was already deleted");
        }

        $data->update([Addresses::DeletedAt->value => new DateTime()]);
    }
}