<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\CancellationPolicy;

/**
 * Clase CancellationPolicyRule.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * condiciones de una politica de cancelación.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class CancellationPolicyRule
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el intervalo de dias
     * superiores para la regla de politicas de cancelación.
     *
     * @var Integer
     */
    protected $upper_bound;

    /**
     * Esta propiedad es usada para interactuar con el intervalo de dias
     * superiores para la regla de politicas de cancelación.
     *
     * @var Integer
     */
    protected $bottom_bound;

    /**
     * Esta propiedad es usada para interactuar con el monto asignado a la
     * regla de politicas de cancelación.
     *
     * @var Float
     */
    protected $variation_amount;

    /**
     * Esta propiedad es usada para interactuar con la politica de cancelación a la
     * que se le fue asignada estas reglas.
     *
     * @var CancellationPolicy
     */
    protected $cancellation_policy;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set upper_bound
     *
     * @param integer $upperBound
     * @return CancellationPolicyRule
     */
    public function setUpperBound($upperBound)
    {
        $this->upper_bound = $upperBound;

        return $this;
    }

    /**
     * Get upper_bound
     *
     * @return integer 
     */
    public function getUpperBound()
    {
        return $this->upper_bound;
    }

    /**
     * Set bottom_bound
     *
     * @param integer $bottomBound
     * @return CancellationPolicyRule
     */
    public function setBottomBound($bottomBound)
    {
        $this->bottom_bound = $bottomBound;

        return $this;
    }

    /**
     * Get bottom_bound
     *
     * @return integer 
     */
    public function getBottomBound()
    {
        return $this->bottom_bound;
    }

    /**
     * Set variation_amount
     *
     * @param float $variationAmount
     * @return CancellationPolicyRule
     */
    public function setVariationAmount($variationAmount)
    {
        $this->variation_amount = $variationAmount;

        return $this;
    }

    /**
     * Get variation_amount
     *
     * @return float 
     */
    public function getVariationAmount()
    {
        return $this->variation_amount;
    }

    /**
     * Set cancellation_policy
     *
     * @param CancellationPolicy $cancellationPolicy
     * @return CancellationPolicyRule
     */
    public function setCancellationPolicy(CancellationPolicy $cancellationPolicy = null)
    {
        $this->cancellation_policy = $cancellationPolicy;

        return $this;
    }

    /**
     * Get cancellation_policy
     *
     * @return CancellationPolicy
     */
    public function getCancellationPolicy()
    {
        return $this->cancellation_policy;
    }

    /**
     * retorna una representacion del objeto como array
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 26-01-2016
     *
     * @return Array
     */
    public function toArray()
    {
        return [
            'upper_bound' => $this->upper_bound,
            'bottom_bound' => $this->bottom_bound,
            'variation_amount' => $this->variation_amount
        ];
    }
}
