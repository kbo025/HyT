<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\GetCompanyData;

use Navicu\Core\Application\Contract\Command;

/**
* comando crear tempowner
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 06/05/2015
*/
class GetCompanyDataCommand implements Command
{
	/**
	 * @var string $username 		usuario registrando la AAVV
	 */
	private $username;



	public function __construct($data = null)
	{
		

		if(isset($data['username'])){
            $this->username = $data['username'];
        }

	}

	public function getRequest()
	{
		return array(
			
			'username'=>$this->username
            
        );
	}
}