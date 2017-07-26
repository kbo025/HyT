<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Model\Entity\CancellationPolicyRule;
use Navicu\Core\Domain\Model\Entity\PropertyCancellationPolicy;
use Navicu\Core\Domain\Model\Entity\Category;

/**
 * Clase CancellationPolicy.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las
 * distintas politicas de cancelación que pueden ser aplicadas en el sistema.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class CancellationPolicy
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     *
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con el valor asignado en la lista de
     * tipos de politicas de cancelación.
     *
     * @var Category Type Object
     */
    protected $variation_type;

    /**
     * Esta propiedad es usada para interactuar con el monto asignado a la
     * politica de cancelación.
     *
     * @var float
     */
    protected $variation_amount;

    /**
     * Esta propiedad es usada para interactuar con el tipo de monto para las reglas de
     * politicas de cancelación.
     *
     * @var Integer
     */
    protected $variation_type_rule;

    /**
     * Esta propiedad es usada para interactuar con las reglas de politica de cancelación.
     *
     * @var CancellationPolicyRule Type Object
     */
    protected $rules;

    /**
     * Esta propiedad es usada para interactuar las listas de las politicas de cancelacion
     * de los establecimiento.
     *
     * @var PropertyCancellationPolicy Type Object
     */
    protected $properties_cancellations_policies;

    /**
     * Esta propiedad es usada para interactuar con el tipo de monto para las politicas de
     * cancelación.
     * @var Category
     */
    protected $type;

    /**
     * Metodo Constructor de php
     */
    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->properties_cancellations_policies = new ArrayCollection();
    }

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
     * Set variation_type
     *
     * @param integer $variationType
     *
     * @return CancellationPolicy
     */
    public function setVariationType($variationType)
    {
        $this->variation_type = $variationType;

        return $this;
    }

    /**
     * Get variation_type
     *
     * @return integer 
     */
    public function getVariationType()
    {
        return $this->variation_type;
    }

    /**
     * Set variation_amount
     *
     * @param float $variationAmount
     *
     * @return CancellationPolicy
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
     * Set variation_type_rule
     *
     * @param integer $variationTypeRule
     *
     * @return CancellationPolicy
     */
    public function setVariationTypeRule($variationTypeRule)
    {
        $this->variation_type_rule = $variationTypeRule;

        return $this;
    }

    /**
     * Get variation_type_rule
     *
     * @return integer 
     */
    public function getVariationTypeRule()
    {
        return $this->variation_type_rule;
    }

    /**
     * Add rules
     *
     * @param CancellationPolicyRule $rule
     *
     * @return CancellationPolicy
     */
    public function addRule(CancellationPolicyRule $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Remove rule
     *
     * @param CancellationPolicyRule $rule
     */
    public function removeRule(CancellationPolicyRule $rule)
    {
        $this->rules->removeElement($rule);
    }

    /**
     * Get rules
     *
     * @return ArrayCollection
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Add properties_cancellations_policies
     *
     * @param PropertyCancellationPolicy $propertiesCancellationsPolicies
     * @return CancellationPolicy
     */
    public function addPropertiesCancellationsPolicy(PropertyCancellationPolicy $propertiesCancellationsPolicies)
    {
        $this->properties_cancellations_policies[] = $propertiesCancellationsPolicies;

        return $this;
    }

    /**
     * Remove properties_cancellations_policies
     *
     * @param PropertyCancellationPolicy $propertiesCancellationsPolicies
     */
    public function removePropertiesCancellationsPolicy(PropertyCancellationPolicy $propertiesCancellationsPolicies)
    {
        $this->properties_cancellations_policies->removeElement($propertiesCancellationsPolicies);
    }

    /**
     * Get properties_cancellations_policies
     *
     * @return ArrayCollection
     */
    public function getPropertiesCancellationsPolicies()
    {
        return $this->properties_cancellations_policies;
    }

    /**
     * Set type
     *
     * @param Category $type
     *
     * @return CancellationPolicy
     */
    public function setType(Category $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Category
     */
    public function getType()
    {
        return $this->type;
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
        $rules = [];

        foreach ($this->rules as $rule)
            $rules[] = $rule->toArray();

        return [
            'variation_type' => $this->variation_type,
            'variation_amount' => $this->variation_amount,
            'variation_type_rule' => $this->variation_type_rule,
            'rules' => $rules
        ];
    }
}
