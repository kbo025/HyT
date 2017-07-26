<?php
namespace Navicu\Core\Application\UseCases\AAVV\Preregister\ConfirmAAVV;

use Navicu\Core\Application\Contract\Command;

/**
* comando confirmar aavv
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 22/08/2015
*/
class ConfirmAAVVCommand implements Command
{

	/**
	 * @var string $userName
	 */
	private $userName;

	/**
	 * @var string $token
	 */
	private $token;


	public function __construct($userName, $token)
	{
		
		if(isset($userName)){
            $this->userName = $userName;
        }

        if(isset($token)){
            $this->token = $token;
        }

	}

	public function getRequest()
	{
		return array(
            'username'=>$this->userName,
            'token'=>$this->token
        );
	}
}
