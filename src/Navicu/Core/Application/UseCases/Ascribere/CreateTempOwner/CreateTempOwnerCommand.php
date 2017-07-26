<?php
namespace Navicu\Core\Application\UseCases\Ascribere\CreateTempOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
* comando crear tempowner
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 06/05/2015
*/
class CreateTempOwnerCommand extends CommandBase implements Command
{
	/**
	 * @var string $password  		contraseña de usuario
	 */
	protected $password;

	/**
	 * @var string $email  			direccion de correo electronico
	 */
	protected $email;

	/**
	 * @var string $username 		Nombre de usuario
	 */
	protected $username;

	public function getUsername() 
	{
		return $this->username;
	}

	public function setUsername($username) 
	{
		$this->username = $username;

		return $this;
	}

	public function getEmail() 
	{
		return $this->email;
	}

	public function setEmail($email) 
	{
		$this->email = $email;

		return $this;
	}

	public function getPassword() 
	{
		return $this->password;
	}

	public function setPassword($password) 
	{
		$this->password = $password;

		return $this;
	}
}