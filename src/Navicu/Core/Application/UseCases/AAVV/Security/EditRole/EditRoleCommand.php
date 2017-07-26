<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\EditRole;

use Navicu\Core\Application\Contract\Command;

/**
* comando crear rol
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 16/09/2016
*/
class EditRoleCommand implements Command
{
	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $name;

	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $id;



	public function __construct($data = null)
	{
		
		if(isset($data['name'])){
            $this->name = $data['name'];
        }

        if(isset($data['id'])){
            $this->id = $data['id'];
        }

	}

	public function getRequest()
	{
		return array(
            'name'=>$this->name,
            'id'=>$this->id
          );
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name=$name;
	}
}
