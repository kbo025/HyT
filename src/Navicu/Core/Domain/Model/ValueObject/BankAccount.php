<?php

namespace Navicu\Core\Domain\Model\ValueObject;

/**
 *
 * Se define una clase objeto mvalor que modela una direccion de correo electronico en un estado valido
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho (05-05-15)
 * 
 */
class BankAccount
{
    /**
     * @var String
     */
    private $entity;

    /**
     * @var String
     */
    private $office;

    /**
     * @var String
     */
    private $control;

    /**
     * @var String
     */
    private $account;

    /**
     * Metodo Constructor de php
     *
     * @param   String  $entity
     * @param   String  $office
     * @param   String  $control
     * @param   String  $account
     *
     */    
    public function __construct($entity, $office, $control, $account)
    {
        if (
            is_string($entity) &&
            is_string($office) &&
            is_string($control) &&
            is_string($account)
        ) {
            if (
                preg_match("/\d{4}/", $entity) &&
                preg_match("/\d{4}/", $office) &&
                preg_match("/\d{2}/", $control) &&
                preg_match("/\d{2,10}/", $account)
            ){
                $this->entity = $entity;
                $this->office = $office;
                $this->control = $control;
                $this->account = $account;
            } else {
                throw new \Exception('Formato de entrada invalido 1');
            }
        } else {
            throw new \Exception('Formato de entrada invalido 2');
        }
    }

    /**
     * devuelve el objeto en su representacion string
     * @return   String
     */
    public function toString()
    {
    	return $this->entity."-".$this->office."-".$this->control."-".$this->account;
    }

    /**
     * devuelve el objeto email en su representacion array
     * @return   Array
     */
    public function toArray()
    {
        return array(
            'entity' => $this->entity,
            'office' => $this->office,
            'control' => $this->control,
            'account' => $this->account
        );
    }
}