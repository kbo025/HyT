<?php
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Adapter\CoreTranslator;

/**
 * Clase LogsOwner
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo
 * del historial de usuario hotelero.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class LogsOwner
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * Esta propiedad es usada para interactuar con la fecha del historial del hotelero.
     * 
     * @var \DateTime
     */
    protected $date;

    /**
     * Esta propiedad es usada para interactuar con la hora del historial del hotelero.
     * 
     * @var \Time
     */
    protected $time;

    /**
     * Esta propiedad es usada para interactuar con el tipo de acción realizada
     * dentro de un recurso (CRUD - Control de Acceso).
     * 
     * @var String
     */
    protected $action;

    /**
     * Esta propiedad es usada para interactuar con el tipo de recurso de un establecimiento
     * con el cual el usuario interactuo.
     * 
     * @var String
     */
    protected $resource;

    /**
     * Esta propiedad es usada para interactuar con el nombre del archivo log.
     *
     * @var String
     */
    protected $file_name;

    /**
     * Esta propiedad es usada para contener la información que sera guardada en el logs
     * Post-Persistencia.
     *
     * @var Array
     */
    protected $temp;

    /**
     * Esta propiedad es usada para interactuar con el perfil de usuario de tipo Propietario.
     * 
     * @var OwnerProfile Type Object
     **/
    protected $owner_profile;

    /**
     * Esta propiedad es usada para interactuar con el perfil de usuario de tipo Propietario.
     *
     * @var Property
     */
    protected $property;

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
     *
     * @return LogsOwner
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
     * Set time
     *
     * @param \DateTime $time
     *
     * @return LogsOwner
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return LogsOwner
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set resource
     *
     * @param string $resource
     *
     * @return LogsOwner
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     *
     * @return LogsOwner
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set owner_profile
     *
     * @param OwnerProfile $ownerProfile
     *
     * @return LogsOwner
     */
    public function setOwnerProfile(OwnerProfile $ownerProfile = null)
    {
        $this->owner_profile = $ownerProfile;

        return $this;
    }

    /**
     * Get owner_profile
     *
     * @return OwnerProfile
     */
    public function getOwnerProfile()
    {
        return $this->owner_profile;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return LogsOwner
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Get logFile
     *
     * @return string
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;
        return $this;
    }

    /**
     * Get date
     *
     * @return String
     */
    public function getStringDate()
    {
        return is_object($this->date) ? $this->date->format('Y-m-d') : $this->date;
    }

    /**
     * Get date
     *
     * @return String
     */
    public function getStringTime()
    {
        return is_object($this->time) ? $this->time->format('h:i A') : $this->time;
    }

    /**
     * Esta función es usada para guardar un logs "Historico",
     * de la grilla en referencia a los dailyPack y dailyRoom.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object $objDailys
     * @return String
     */
    public function saveLogsDaily($ownerProfile, $collectionDailys, $oldData)
    {
        $response["new"] = array();

        $user = get_class($ownerProfile) == "Proxies\__CG__\Navicu\Core\Domain\Model\Entity\OwnerProfile" ? $ownerProfile : null;
        $this->setDate(date_create());
        $this->setTime(date_create());
        $this->setOwnerProfile($user);
        
        $response["modificationDate"] = $collectionDailys[0]->getDate()->format('Y-m-d');
        if (get_class($collectionDailys[0]) == "Navicu\Core\Domain\Model\Entity\DailyPack") {
            $this->setProperty($collectionDailys[0]->getPack()->getRoom()->getProperty());
            $this->setResource("dailyPack");
            $response["new"]["room"] = $collectionDailys[0]->getPack()->getRoom()->getName();
            $response["new"]["pack"] = CoreTranslator::getTranslator($collectionDailys[0]->getPack()->getType()->getCode());
        } else {
            $this->setProperty($collectionDailys[0]->getRoom()->getProperty());
            $this->setResource("dailyRoom");
            $response["new"]["room"] = $collectionDailys[0]->getRoom()->getName();
        }

        $flag = 0; //evitar que salga en la lista de afectados el pack anfitrion
        foreach ($collectionDailys as $objDaily) {
            if ($flag == 0) {
                $flag = 1;
            } else {
                if (!isset($response["new"]["affected"])) {
                    $response["new"]["affected"] = array();
                }

                if (get_class($objDaily) == "Navicu\Core\Domain\Model\Entity\DailyPack") {
                    $affected["room"] = $objDaily->getPack()->getRoom()->getName();
                    $affected["pack"] = CoreTranslator::getTranslator($objDaily->getPack()->getType()->getCode());
                } else {
                    $affected["room"] = $objDaily->getRoom()->getName();
                }

                array_push($response["new"]["affected"], $affected);
            }
        }

        $newData = $collectionDailys[0]->getArray();

        if (is_null($oldData)) {
            unset($newData["idDailyPack"]);
            unset($newData["idDailyRoom"]);
            unset($newData["idPack"]);
            unset($newData["idRoom"]);
            unset($newData["isCompleted"]);
            $response = array_merge($newData, $response["new"]);
            $this->setAction("Created");
            $this->setTemp(array("new" => $response));
            return $this;
        } else {
            $this->setAction("Update");
            $response["old"] = array();

            unset($newData["isCompleted"]);
            unset($oldData["isCompleted"]);
            
            foreach ($newData as $key => $value) {
                if (isset($value) && $key != "room" && $key != "pack"&& $key != "affected") {
                    if (isset($oldData["$key"]) or $oldData["$key"] == null) {
                        if ($newData["$key"] !== $oldData["$key"] ){
                            $response["old"]["$key"] = $oldData["$key"];
                            $response["new"]["$key"] = $newData["$key"];
                        }
                    }
                }
            }

            if (count($response["old"]) != 0) {
                $this->setTemp($response);
                return $this;
            } else {
                return null;
            }

        }

    }

     /**
     * Esta función es usada para guardar un logs "Historico",
     * de carga masiva.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object $objDailys
     * @return String
     */
    public function saveLogsMassLoad($command, $rf)
    {
        $data = $command->get('data');
        $property = $rf->get('Property')->findBySlug($command->get('slug'));
        $user = get_class($command->get('userSession')) == "Proxies\__CG__\Navicu\Core\Domain\Model\Entity\OwnerProfile" ? $command->get('userSession') : null;
        
        $this->setDate(date_create());
        $this->setTime(date_create());
        $this->setAction("Update");
        $this->setResource("massLoad");
        $this->setProperty($property);
        $this->setOwnerProfile($user);

        foreach ($data["rooms"] as &$rooms) {
            if($command->isApiRequest())
                $rooms["name"] = $rf->get('Room')->findOneBy(array('slug' => $rooms["idRoom"]))->getName();
            else
                $rooms["name"] = $rf->get('Room')->findById($rooms["idRoom"])->getName();

            foreach ($rooms["packages"] as &$pakages) {
                if($command->isApiRequest())
                    $pakages["name"] = CoreTranslator::getTranslator($rf->get('Pack')->findOneBy(array('slug' => $pakages["idPack"]))->getType()->getCode());
                else
                    $pakages["name"] = CoreTranslator::getTranslator($rf->get('Pack')->findById($pakages["idPack"])->getType()->getCode());
            }
        }
        $this->setTemp($data);
        return $this;
    }

    /**
    * Esta función es usada para crear y guardar un archivo .log
    * dentro del sistema con los datos almacenados en la post-persistencia
    *
    * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
    * @author Currently Working: Joel D. Requena P.
    *
    * @param void
    * @return Object LogsOwner
    */
    public function createLog()
    {
        global $kernel;

        $fileName = $this->getId().sha1(uniqid(mt_rand(), true));

        $contnt = serialize($this->temp);
        $fp = fopen($kernel->getRootDir()."/logs/extranet/$fileName.log", "x");
        fwrite($fp, print_r($contnt, TRUE));
        fclose($fp);

        $this->file_name = $fileName;
        return $this;
    }

    /**
    * Esta función es usada para retornar la dirección fisica de
    * un archivo log.
    *
    * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
    * @author Currently Working: Joel D. Requena P.
    *
    * @return string
    */
    public function getPath()
    {
        global $kernel;

        return $kernel->getRootDir()."/logs/extranet/".$this->file_name.".log";
    }

    /**
     * Función para el manejo de la información por
     * medio de un arreglo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function getArray()
    {
        $data['date'] = $this->getStringDate();
        $data['time'] = $this->getStringTime();
        $data['action'] = $this->action;
        $data['resource'] = $this->resource;
        $data['fileName'] = $this->file_name;
        $data['ownerProfile'] = $this->owner_profile ? $this->owner_profile->getUser()->getUserName() : "Navicu";
        $data['property'] = $this->property->getName();

        return $data;
    }

    
    /**
     * Función para el manejo de la información guardada en
     * el archivo logs.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @return Array
     */
    public function getOpenFile()
    {
        return unserialize(fgets(fopen($this->getPath() ,"r")));
    }
}
