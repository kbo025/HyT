<?php

namespace Navicu\Core\Application\UseCases\EditClientProfile;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\InfrastructureBundle\Repositories\DbClientProfileRepository;

/**
 * Conjunto de objetos para editar los valores ingresados del cliente perteneciente al caso de uso EditClientProfile
 * @author	Isabel Nieto
 */
class EditClientProfileCommand implements Command
{
	/**
	 * @var object $client_profile es un objeto de la clase ClientProfile
	 */
	private $client_profile;

	/**
	 * @var string $full_name Nombre y Apellido del cliente
	 */
	private $full_name;

	/**
	 * @var string $identity_card es la identificacion del cliente
	 */
	private $identity_card;

	/**
	 * @var integer $gender genero del cliente (1/0)
	 */
	private $gender;

	/**
	 * @var string $email es el email del cliente
	 */
	private $email;

	/**
	 * @var string $phone numero de telefono del usuario
	 */
	private $phone;

	/**
	 * @var boolean $email_news si quiere recibir informacion extra por parte de navicu
	 */
	private $email_news;

	/**
	 * @var string $birth_date fecha de nacimiento
	 */
	private $birth_date;

	/**
	 * @var string $address direccion especifica del usuario
	 */
	private $address;

	/**
	 * @var integer $state estado al que pertenece
	 */
	private $state;

	/**
	 * @var integer $country ciudad a la que pertenece
	 */
	private $country;

	/**
	 * @var string $zip_code codigo postal de la ciudad a la cual pertenece el usuario
	 */
	private $zip_code;

	/**
	 * @var ArrayCollection $hobbies coleccion de hobbies pertenecientes a un cliente
	 */
	private $hobbies;

	/**
	 * @var ArrayCollection $profession coleccion de profesiones pertenecientes a un cliente
	 */
	private $profession;

	/**
	 * devolver al cliente que esta siendo ingresado desde la session
	 * @return object
	 */
	public function getClientProfile()
	{
		return $this->client_profile;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->full_name;
	}

	/**
	 * @param string $full_name
	 */
	public function setFullName($full_name)
	{
		$this->full_name = $full_name;
	}

	/**
	 * @return string
	 */
	public function getIdentityCard()
	{
		return $this->identity_card;
	}

	/**
	 * @param string $identity_card
	 */
	public function setIdentityCard($identity_card)
	{
		$this->identity_card = $identity_card;
	}

	/**
	 * @return int
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * @param int $gender
	 */
	public function setGender($gender)
	{
		$this->gender = $gender;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return boolean
	 */
	public function isEmailNews()
	{
		return $this->email_news;
	}

	/**
	 * @param boolean $email_news
	 */
	public function setEmailNews($email_news)
	{
		$this->email_news = $email_news;
	}

	/**
	 * @return string
	 */
	public function getBirthDate()
	{
		return $this->birth_date;
	}

	/**
	 * @param string $birth_date
	 */
	public function setBirthDate($birth_date)
	{
		$this->birth_date = $birth_date;
	}

	/**
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address)
	{
		$this->address = $address;
	}

	/**
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param int $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}

	/**
	 * @return int
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param int $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function getZipCode()
	{
		return $this->zip_code;
	}

	/**
	 * @param string $zip_code
	 */
	public function setZipCode($zip_code)
	{
		$this->zip_code = $zip_code;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getHobbies()
	{
		return $this->hobbies;
	}

	/**
	 * @param ArrayCollection $hobbies
	 */
	public function setHobbies($hobbies)
	{
		$this->hobbies = $hobbies;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getProfession()
	{
		return $this->profession;
	}

	/**
	 * @param ArrayCollection $profession
	 */
	public function setProfession($profession)
	{
		$this->profession = $profession;
	}

	/**
	 * EditClientProfileCommand constructor.
	 * @param object $clientProfile objeto tipo ClientProfile obtenido desde la session en curso
	 * @param string $userInfo valores nuevos a ingresar
	 */
	public function __construct($clientProfile, $userInfo)
	{
		if (isset($userInfo['birthDate']))
			$this->birth_date = $userInfo['birthDate'];
		if (isset($userInfo['email']))
			$this->email = $userInfo['email'];
		if (isset($userInfo['emailNews'])) 
			$this->email_news = $userInfo['emailNews'];
		if (isset($userInfo['fullName']))
			$this->full_name = $userInfo['fullName'];
		if (isset($userInfo['gender']))
			$this->gender = $userInfo['gender'];
		if (isset($userInfo['identityCard']))
			$this->identity_card = $userInfo['identityCard'];
		if (isset($userInfo['phone']))
			$this->phone = $userInfo['phone'];
		if (isset($userInfo['address']))
			$this->address = $userInfo['address'];
		if (isset($userInfo['zipCode']))
			$this->zip_code = $userInfo['zipCode'];
		if (isset($userInfo['state']))
			$this->state = $userInfo['state'];
		if (isset($userInfo['country']))
			$this->country = $userInfo['country'];
		if (isset($userInfo['hobbies']))
			$this->hobbies = $userInfo['hobbies'];
		if (isset($userInfo['professions']))
			$this->profession = $userInfo['professions'];
		$this->client_profile = $clientProfile;
	}

	/**
	 * Funcion encorgada de asignar y devolver los parametros obtenidos desde el constructor
	 * @return array
	 */
	public function getRequest()
	{
		return array(
			'client_profile' => $this->client_profile,
			'fullName' => $this->full_name,
			'identityCard' => $this->identity_card,
			'gender' => $this->gender,
			'email' => $this->email,
			'phone' => $this->phone,
			'emailNews' => $this->email_news,
			'birthDate' => ($this->birth_date != null) ? $this->birth_date : null,
			'address' => $this->address,
			'zipCode' => $this->zip_code,
			'state' => $this->state,
			'country' => $this->country,
			'hobbies' => $this->hobbies,
			'professions' => $this->profession
		);
	}
}