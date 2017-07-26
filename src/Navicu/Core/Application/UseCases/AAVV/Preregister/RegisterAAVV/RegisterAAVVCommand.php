<?php
namespace Navicu\Core\Application\UseCases\AAVV\Preregister\RegisterAAVV;

use Navicu\Core\Application\Contract\Command;

/**
* comando crear tempowner
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 06/05/2015
*/
class RegisterAAVVCommand implements Command
{
	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $password;

	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $confirmpassword;

	/**
	 * @var string $email  			direccion de correo electronico
	 */
	private $email;

	/**
	 * @var string $username 		Nombre de usuario
	 */
	private $fullname;

	/**
	 * @var string $username 		Nombre de usuario
	 */
	private $phone;


	public function __construct($data = null)
	{
		
		if(isset($data['password'])){
            $this->password = $data['password'];
        }

        if(isset($data['confirmpassword'])){
            $this->confirmpassword = $data['confirmpassword'];
        }

        if(isset($data['email'])){
            $this->email = $data['email'];
        }

        if(isset($data['fullname'])){
            $this->fullname = $data['fullname'];
        }

        if(isset($data['phone'])){
            $this->phone = $data['phone'];
        }

	}

	public function getRequest()
	{
		return array(
            'fullname'=>$this->fullname,
            'email'=>$this->email,
            'password'=>$this->password,
            'confirmpassword'=>$this->confirmpassword,
            'phone'=>$this->phone
        );
	}

	public function getFullName()
	{
		return $this->fullname;
	}

	public function getEmail()
	{
		return $this->email;	
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setFullName($fullname)
	{
		$this->fullname=$fullname;
	}

	public function setEmail($email)
	{
		$this->email=$email;
	}

	public function setPassword($password)
	{
		$this->password=$password;
	}
}
