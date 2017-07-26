<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\SetCompanyData;

use Navicu\Core\Application\Contract\Command;

/**
* comando crear tempowner
* @author Alejandro Conde <adcs2008@gmail.com>
* @author Currently Working: Alejandro Conde
* @version 06/05/2015
*/
class SetCompanyDataCommand implements Command
{
	/**
	 * @var string $username 		usuario registrando la AAVV
	 */
	private $username;

	/**
	 * @var string $password  		contraseña de usuario
	 */
	private $commercial_name;

	/**
	 * @var string $email  			direccion de correo electronico
	 */
	private $social_reason;

	/**
	 * @var string $username 		Nombre de usuario
	 */
	private $rif;

	/**
	 * @var string $password  		contraseña de usuario
	 */
	//private $merchant_id;

	/**
	 * @var integer $status  		estado del registro mercantil
	 */
	//private $status;

	/**
	 * @var string $email  			direccion de correo electronico
	 */
	private $company_email;

	/**
	 * @var string $username 		Nombre de usuario
	 */
	private $phone;

	private $opening_year;

	private $longitude;

	private $latitude;

	private $city;

	private $address;

	private $zip_code;

	private $users;

    private $form;



	public function __construct($data = null)
	{
		
		/*if(isset($data['zip_code'])){
            $this->zip_code = $data['zip_code'];
        }

		if(isset($data['address'])){
            $this->address = $data['address'];
        }

		if(isset($data['longitude'])){
            $this->longitude = $data['longitude'];
        }

        if(isset($data['latitude'])){
            $this->latitude = $data['latitude'];
        }

		if(isset($data['city'])){
            $this->city = $data['city'];
        }

		if(isset($data['username'])){
            $this->username = $data['username'];
        }

		if(isset($data['commercial_name'])){
            $this->commercial_name = $data['commercial_name'];
        }

        if(isset($data['social_reason'])){
            $this->social_reason = $data['social_reason'];
        }

        if(isset($data['rif'])){
            $this->rif = $data['rif'];
        }

        if(isset($data['merchant_id'])){
            $this->merchant_id = $data['merchant_id'];
        }

        if(isset($data['status'])){
            $this->status = $data['status'];
        }

        if(isset($data['company_email'])){
            $this->company_email = $data['company_email'];
        }

        if(isset($data['phone'])){
            $this->phone = $data['phone'];
        }

        if(isset($data['opening_year'])){
            $this->opening_year = $data['opening_year'];
        }*/

        if(isset($data['users'])){
            $this->users = $data['users'];
        }

        if(isset($data['form'])) {
            $this->form = $data['form'];
        }

	}

	public function getRequest()
	{
		return array(
			'users'=>$this->users,
            'form' => $this->form
			/*'zip_code'=>$this->zip_code,
			'address'=>$this->address,
			'longitude'=>$this->longitude,
			'latitude'=>$this->latitude,
			'city'=>$this->city,
			'username'=>$this->username,
            'commercial_name'=>$this->commercial_name,
            'social_reason'=>$this->social_reason,
            'rif'=>$this->rif,
            'merchant_id'=>$this->merchant_id,
            'status'=>$this->status,
            'company_email'=>$this->company_email,
            'phone'=>$this->phone,
            'opening_year'=>$this->opening_year*/
        );
	}
}