<?php
namespace Navicu\Core\Domain\Adapter;

use Navicu\Core\Domain\Adapter\EntityValidationException;

/**
* esta clase modela el comportamiento basico de una maquina de estados finita para ser heredado por las entidades del modelo
* que se comporten de este modo
*
* @author Gabriel Camacho <kboo025@gmail.com>
* @author Currently Working: Gabriel Camacho
*/
class StateMachine 
{

    /**
    * representa el estado actual de la entidad
    */
    protected $state;

    /**
    * lista de estados posibles de la entidad
    * 
    * <nombre_del_estado> => <valor_del_estado>
    */
    protected $states = [];

    /**
    * lista de las tranciciones permitidas para el estado del objeto
    * 
    * <nombre_de_la_transicion> => [<estado_origen>,<estado_destino>]
    */
    protected $transitions = [];

    /**
    * lista de llamadas callback definidas para cada transicion 
    * 
    * <nombre_de_la_transicion> => [
    *   'before' => ['functionName' => <nombre_de_la_funcion>],
    *   'after' => ['functionName' => <nombre_de_la_funcion>],
    * ]
    */
    protected $callbacks = [];

    /**
    * indica si la transicion puede ser ejecutada
    */
    public function can($newState)
    {
        return array_search([$this->state,$newState],$this->transitions);
    }

    /**
    * devuelve el estado actual del objeto
    */
    public function getState()
    {
        return $this->state;
    }

    /**
    * ejecuta una trancision si esta es permitida
    */
    public function setState($newState)
    {
        $index = $this->can($newState); 

        if (($this->state != $newState) && ($index === false))
            throw new EntityValidationException('state',get_class($this),'prohibited_transition '.$this->state.'-'.$newState,0);
        
        $this->state = $newState;

        return $this;
    }

    /**
    * devuelve el nombre asociado al estado de un objeto
    */
    public function getStateName()
    {
        return array_search($this->state,$this->states);
    }
}