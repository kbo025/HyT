<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\CreateRole;

use Navicu\Core\Application\Contract\Command;

/**
* comando crear rol
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 16/09/2016
*/
class CreateRoleCommand implements Command
{
	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $name;



	public function __construct($data = null)
	{
		
		if(isset($data['name'])){
            $this->name = $data['name'];
        }


	}

	public function getRequest()
	{
		return array(
            'name'=>$this->name
          );
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}
}
