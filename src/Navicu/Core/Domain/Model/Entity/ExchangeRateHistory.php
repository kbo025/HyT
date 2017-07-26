<?php

namespace Navicu\Core\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tabla para el manejo de divisas
 *
 * ExchangeRateHistory
 */
class ExchangeRateHistory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * fecha donde se hizo la consulta a una API
     * @var \Date
     */
    private $date;

    /**
     * Costo paralelo o cambio equivalente a otra moneda
     * @var float
     */
    private $rate_api;

    /**
     * @var integer
     */
    private $percentage_navicu;

    /**
     * Valor real que describe para el caso de EUR y USD el costo paralelo por dolar today
     * Para las demas monedas describe el equivalente a USD
     * @var float
     */
    private $rate_navicu;

    /**
     * Costo del dolar oficial
     * @var float
     */
    private $dicom;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\CurrencyType
     */
    private $currency_type;

    public function __construct()
    {
        $this->date = new \DateTime("now");
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
     * Set date
     *
     * @param \DateTime $date
     * @return ExchangeRateHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set rate_api
     *
     * @param float $rateApi
     * @return ExchangeRateHistory
     */
    public function setRateApi($rateApi)
    {
        $this->rate_api = $rateApi;

        return $this;
    }

    /**
     * Get rate_api
     *
     * @return float
     */
    public function getRateApi()
    {
        return $this->rate_api;
    }

    /**
     * Set percentage_navicu
     *
     * @param integer $percentageNavicu
     * @return ExchangeRateHistory
     */
    public function setPercentageNavicu($percentageNavicu)
    {
        $this->percentage_navicu = $percentageNavicu;

        return $this;
    }

    /**
     * Get percentage_navicu
     *
     * @return integer
     */
    public function getPercentageNavicu()
    {
        return $this->percentage_navicu;
    }

    /**
     * Set rate_navicu
     *
     * @param float $rateNavicu
     * @return ExchangeRateHistory
     */
    public function setRateNavicu($rateNavicu)
    {
        $this->rate_navicu = $rateNavicu;

        return $this;
    }

    /**
     * Get rate_navicu
     *
     * @return float
     */
    public function getRateNavicu()
    {
        return $this->rate_navicu;
    }

    /**
     * Set dicom
     *
     * @param float $dicom
     * @return ExchangeRateHistory
     */
    public function setDicom($dicom)
    {
        $this->dicom = $dicom;

        return $this;
    }

    /**
     * Get dicom
     *
     * @return float
     */
    public function getDicom()
    {
        return $this->dicom;
    }

    /**
     * Set currency_type
     *
     * @param \Navicu\Core\Domain\Model\Entity\CurrencyType $currencyType
     * @return ExchangeRateHistory
     */
    public function setCurrencyType(\Navicu\Core\Domain\Model\Entity\CurrencyType $currencyType = null)
    {
        $this->currency_type = $currencyType;

        return $this;
    }

    /**
     * Get currency_type
     *
     * @return \Navicu\Core\Domain\Model\Entity\CurrencyType
     */
    public function getCurrencyType()
    {
        return $this->currency_type;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        // Add your code here
    }

    /**
     * Funcin para devolver informacion de la entidad
     * @return mixed
     */
    public function getArray()
    {
        $data['date'] = ($this->date->format("Y-m-d"));
        $data['percentage_navicu'] = $this->percentage_navicu;
        $data['rate_api'] = $this->rate_api;
        $data['rate_navicu'] = $this->rate_navicu;
        $data['dicom'] = $this->dicom;
        $data['currency_type'] = $this->currency_type->getAlfa3();

        return $data;
    }

    /**
     * Función para la creación de un LogsUser a partir de un
     * arreglo con información basica.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param array $data, array con los parametros a ser guardados
     * @return Array
     */
    public function updateObject($data)
    {
        $this->percentage_navicu = isset($data['percentage_navicu']) ? $data['percentage_navicu'] : null;
        $this->rate_api = isset($data['rate_api']) ? $data['rate_api'] : null;
        $this->rate_navicu= isset($data['rate_navicu']) ? $data['rate_navicu'] : null;
        $this->dicom = isset($data['dicom']) ? $data['dicom'] : null;
        $this->currency_type = isset($data['currency_type']) ? $data['currency_type'] : null;

        return $this;
    }
}
