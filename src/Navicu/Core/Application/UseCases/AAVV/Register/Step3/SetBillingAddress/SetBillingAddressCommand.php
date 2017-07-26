<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 08/09/16
 * Time: 08:39 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step3\SetBillingAddress;


use Navicu\Core\Application\Contract\Command;

class SetBillingAddressCommand implements Command
{
    /**
     * La entidad agencia de viajes a la que pertenece la direccion nueva a crear o modificar
     * @var null
     */
    private $aavv;

    /**
     * String con la direccion de facturacion de aavv
     * @var $address
     */
    private $address;

    /**
     * String con los numero de la cuenta de la aavv
     * @var $bank_account
     */
    private $bank_account;
    /**
     * @var $email string, correo electronico de la direccion de facturacion de la aavv
     */
    private $email;
    /**
     * @var $location integer, numero que describe la localidad de la direccion de facturacion
     */
    private $location;
    /**
     * @var $phone string, numero telefonico de la direccion de facturacion
     */
    private $phone;
    /**
     * @var $swift string, codigo de bancos
     */
    //private $swift;
    /**
     * @var int $type_address, tipo de direccion 0 = direccion cualquiera, direccion 2 = direccion de facturacion
     */
    private $type_address;
    /**
     * @var $zip_code string, codigo de area de la direccion ingresada
     */
    private $zip_code;
    /**
     * @var $personalized_mail boolean, atributo que indica si recibira correos personalizados o no
     */
    private $personalized_mail;
    /**
     * @var $personalized_interface boolean, atributo que indica si tiene personalizacion de interfaz o no
     */
    private $personalized_interface;
    /**
     * @var $rif string, atributo que indica el rif de la empresa
     */
    private $rif;

    public function __construct($data)
    {
        $this->personalized_mail = isset($data['personalized_mail']) ? $data['personalized_mail'] : null;
        $this->personalized_interface = isset($data['personalized_interface']) ? $data['personalized_interface'] : null;
        $this->aavv = isset($data['slug']) ? $data['slug'] : null;
        $this->address = isset($data['address']) ? $data['address'] : null;
        $this->bank_account = isset($data['bank_account']) ? $data['bank_account'] : null;
        $this->email = isset($data['email']) ? $data['email'] : null;
        $this->location = isset($data['location']) ? $data['location'] : null;
        $this->phone = isset($data['phone']) ? $data['phone'] : null;
        //$this->swift = isset($data['swift']) ? $data['swift'] : null;
        $this->type_address = 2;
        $this->zip_code = isset($data['zip_code']) ? $data['zip_code'] : null;
        $this->rif = isset($data['rif']) ? $data['rif'] : null;
    }
    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            'aavv' => $this->aavv,
            'address' => $this->address,
            'bank_account' => $this->bank_account,
            'email' => $this->email,
            'location' => $this->location,
            'phone' => $this->phone,
            //'swift' => $this->swift,
            'type_address' => $this->type_address,
            'zip_code' => $this->zip_code,
            'personalized_mail' => $this->personalized_mail,
            'personalized_interface' => $this->personalized_interface,
            'rif' => $this->rif
        ];
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getBankAccount()
    {
        return $this->bank_account;
    }

    /**
     * @param mixed $bank_account
     */
    public function setBankAccount($bank_account)
    {
        $this->bank_account = $bank_account;
    }

    /**
     * @return mixed
     */
    /*public function getSwift()
    {
        return $this->swift;
    }*/

    /**
     * @param mixed $swift
     */
    /*public function setSwift($swift)
    {
        $this->swift = $swift;
    }*/

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getTypeAddress()
    {
        return $this->type_address;
    }

    /**
     * @param mixed $type_direction
     */
    public function setTypeAddress($type_address)
    {
        $this->type_address = $type_address;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * @param mixed $zip_code
     */
    public function setZipCode($zip_code)
    {
        $this->zip_code = $zip_code;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getAavv()
    {
        return $this->aavv;
    }

    /**
     * @param mixed $aavv
     */
    public function setAavv($aavv)
    {
        $this->aavv = $aavv;
    }
}