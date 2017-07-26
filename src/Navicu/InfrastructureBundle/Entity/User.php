<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\InfrastructureBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\Entity\ReservationChangeHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Clase User.
 *
 * Se define una clase y una serie de propiedades necesarias para el manejo de las cuentas de usuarios en Symfony.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * 
 */
class User extends BaseUser
{
    const USER_DISABLED_ADVANCE = 0;
    const USER_ACTIVATED_ADVANCE  = 1;

    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;

    /**
     * @var date
     */
    private $createdAt;
    /**
     * @var integer
     */
    private $createdBy;
    /**
     * @var date
     */
    private $updatedAt;
    /**
     * @var integer
     */
    private $updatedBy;

    /**
     * indica si la antelación para el usuario esta desactivada o no
     *
     * @var boolean
     */
    private $disable_advance;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $advance_deactivations;

    /**
     * Esta propiedad es usada para interactuar con el perfil de usuario de tipo Propietario.
     * 
     * @var OwnerProfile Type Object
     **/
    protected $owner_profile;

    /**
     * Esta propiedad es usada para interactuar con el perfil de usuario temporal.
     *
     * @var OwnerProfile Type Object
     **/
    protected $temp_owner;

    /**
     * Esta propiedad es usada para interactuar con el perfil de usuario temporal.
     *
     * @var OwnerProfile Type Object
     **/
    protected $reservation_change_history;

    /**
     * Esto atributo es para interactuar con el cliente de navicu.
     *
     * @var ClientProfile Type Object
     **/
    protected $client_profile;

    /**
     * Esto atributo es para interactuar con las notificaciones
     * que este usuario a enviado.
     *
     * @var Object Notification
     **/
    protected $notification_output;

    /**
     * Esto atributo es para interactuar con las notificaciones
     * que este usuario a recibido
     *
     * @var Object Notification
     **/
    protected $notification_input;

    /**
     * Perfil del usuario Comercial
     *
     * @var CommercialProfile Type Object
     */
    private $commercial_profile;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\LogsUser
     */
    private $logs_users;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\NvcProfile
     */
    private $nvc_profile;

    /**
     * @var \Navicu\Core\Domain\Model\Entity\AAVVProfile
     */
    private $aavv_profile;

    /**
     * Implementación de una lista de roles para los usuarios de tipo Propietario y tipo Cliente.
     * 
     * @throws Exception
     * @param Rol $rol 
     */

    /**
     * @var ArrayCollection
     */
    protected $role;

    /**
     * Representa el subdominio asociado a un usuario
     *
     * @var \Navicu\InfrastructureBundle\Entity\Subdomain
     */
    private $subdomain;

    /**
     * desactivaciones de antelacion aplicadas por este usuario
     * 
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deactivations_applied;

    public function __construct()
    {
        parent::__construct();
        $this->role = new ArrayCollection();
    }

    public function getRolesList()
    {
        return $this->role;
    }

    public function getRoles()
    {
        if (! $this->role->count()) {
            return array(parent::ROLE_DEFAULT);
        }
        $roles = $this->role->toArray();
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        foreach ($roles as $k => $role) {
            /* 
             * Ensure String[] to prevent bad unserialized UsernamePasswordToken with for instance 
             * UPT#roles:{Role('ROLE_USER'), 'ROLE_USER'} which ends in Error: Call to a member 
             * function getRole() on a non-object
             */
            $roles[$k] = $role instanceof RoleInterface ? $role->getRole() : (string) $role; 
        }

    
        
        return array_flip(array_flip($roles));
    }

    public function addRole($role)
    {
        //! ($role instanceof Role) && $role = new Role($role);

        $role->addUser($this, false);
        $this->role->add($role);
        return $this;
    }

    public function removeRole($role)
    {
        $role = $this->role->filter(
                    function(Role $r) use ($role) {
                        if ($role instanceof Role) {
                            return $r->getRole() === $role->getRole();
                        } else {
                            return $r->getRole() === strtoupper($role);
                        }
                    }
                )->first();
        if ($role) {
            $this->role->removeElement($role);
        }    
        return $this;
    }

    public function getModulesAccess()
    {
        $roles = $this->getRolesList()->toArray();
        $modules = Array();
        foreach ($roles as $role) {
            $mods = $role->getModules()->toArray();
            foreach($mods as $mod){
                if(!in_array($mod, $modules, true))
                    array_push($modules, $mod);
            }
        }

        return $modules;

    }

    public function getPermissions()
    {
        $roles = $this->getRolesList()->toArray();
        $response = Array();
        foreach ($roles as $role) {
            $perms = $role->getPermissions()->toArray();
            foreach($perms as $perm){
                $perminstance['permission'] = $perm->getName();
                $perminstance['module'] = $perm->getModule()->getName();
                $perminstance['role'] = $role->getName();
                $response[] = $perminstance;

            }
        }

        return $response;
    }

    public function hasAccess($module, $permission = null)
    {
        $permissions = $this->getPermissions();

        $response = false;

        foreach($permissions as $currentperm) {
            if($currentperm['module'] == $module && $currentperm['permission'] == $permission) {
                $response = true;
                break;
            }
        }

        return $response;
    }

    /**
     * elimina un rol de un
     *
     * @param $role
     */
    public function deleteRole($role)
    {
        switch($role){
            case 0:
                $role_text = 'ROLE_ADMIN';
                break;
            case 1:
                $role_text = 'ROLE_EXTRANET_ADMIN';
                break;
            case 2:
                $role_text = 'ROLE_EXTRANET_USER';
                break;
            case 3:
                $role_text = 'ROLE_TEMPOWNER';
                break;
            case 4:
                $role_text = 'ROLE_WEB';
                break;
            case 5:
                $role_text = 'ROLE_COMMERCIAL';
                break;
            case 6:
                $role_text = 'ROLE_AAVV'; /**ROL AGENCIA VIAJERO*/
                break;
            case 7:
                $role_text = 'ROLE_DIR_COMMERCIAL'; /** ROL DIRECTOR COMERCIAL */
                break;
            case 8:
                $role_text = 'ROLE_SALES_EXEC'; /** ROL EJECUTIVO DE VENTA */
                break;
            case 9:
                $role_text = 'ROLE_TELEMARKETING'; /** ROL TELEMARKETING */
                break;
            case 10:
                $role_text = 'ROLE_CALL_CENTER'; /** ROL ATENCION AL CLIENTE */
                break;
            case 11:
                $role_text = 'ROLE_DIR_FINANCIAL'; /** ROL DIRECTOR FINANCIERO */
                break;
            case 12:
                $role_text = 'ROLE_ACCOUNTING'; /** ROL CONTABILIDAD */
                break;
            case 13:
                $role_text = 'ROLE_HHRR'; /** ROL  RECURSOS HUMANO */
                break;
            case 14:
                $role_text = 'ROLE_FINANCIAL_ASSISTANT'; /** ROL ASISTENTE ADMINISTRATIVO  */
                break;
            case 15:
                $role_text = 'ROLE_ADMIN'; /** ROL SUPER ADMIN */
                break;
            case 16:
                $role_text = 'ROLE_DIR_GENERAL'; /** ROL   DIRECTOR GENERAL*/
                break;
            case 17:
                $role_text = 'ROLE_DIR_MARKETING'; /** ROL DIRECTOR MARKETING */
                break;
            case 18:
                $role_text = 'ROLE_MANAGER_SOCIAL'; /** ROL SOCIAL MANAGER */
                break;
            case 19:
                $role_text = 'ROLE_MANAGER_CONTENT'; /** ROL CONTENT MANAGER*/
                break;
            case 20:
                $role_text = 'ROLE_MARKETING_ONLINE '; /** ROL Marketing online */
                break;
            case 21:
                $role_text = 'ROLE_WEB'; /** ROL NVC CLIENTE */
                break;
            case 22:
                $role_text = 'DIR_ROLE_DEVELOPER'; /** ROL DIRECTOR DE DESARROLLO*/
                break;
            case 23:
                $role_text = 'ROLE_DEVELOPER'; /** ROL   DESARROLLADOR*/
                break;
            default: $role_text = 'ROLE_WEB'; break;
        }
        $i = array_search($role_text,$this->roles);
        if($i !== false)
            unset($this->roles[$i]);
    }


    /**
     * Esta funcion devuelve los datos a frontend para LISTAR roles al crear usuarios
     * @author mary sanchez <msmarycarmen@gmail.com>
     * @version 09/06/2016
     * @return mixed
     */
    public function GetInfoCreateUser()
    {
        $response[0]["role"]="SUPER ADMINISTRADOR";
        $response[0]["id"]=0;
        $response[1]["role"]="ADMINISTRADOR HOTELERO";
        $response[1]["id"]=1;
        $response[2]["role"]="ASISTENTE DE HOTELERO";
        $response[2]["id"]=2;
        $response[3]["role"]="HOTELERO EN REGISTRO";
        $response[3]["id"]=3;
        $response[4]["role"]="CLIENTE";
        $response[4]["id"]=4;
        $response[5]["role"]="COMERCIAL";
        $response[5]["id"]=5;
        $response[6]["role"]="AGENCIA DE VIAJE";
        $response[6]["id"]=6;
        $response[7]["role"]="DIRECTOR COMERCIAL";
        $response[7]["id"]=7;
        $response[8]["role"]="EJECUTIVO DE VENTA";
        $response[8]["id"]=8;
        $response[9]["role"]="TELEMARKETING";
        $response[9]["id"]=9;
        $response[10]["role"]="ATENCION AL CLIENTE";
        $response[10]["id"]=10;
        $response[11]["role"]="DIRECTOR FINANCIERO";
        $response[11]["id"]=11;
        $response[12]["role"]="CONTABILIDAD";
        $response[12]["id"]=12;
        $response[13]["role"]="RECURSO HUMANO";
        $response[13]["id"]=13;
        $response[14]["role"]="ASISTENTE FINANCIERO";
        $response[14]["id"]=14;
        $response[15]["role"]="ADMINISTRADOR";
        $response[15]["id"]=15;
        $response[16]["role"]="DIRECTOR GENERAL";
        $response[16]["id"]=16;
        $response[17]["role"]="DIRECTOR MARKETING";
        $response[17]["id"]=17;
        $response[18]["role"]="RED SOCIAL";
        $response[18]["id"]=18;
        $response[19]["role"]="GESTOR DE CONTENIDOS";
        $response[19]["id"]=19;
        $response[20]["role"]="MARKETING ONLINE";
        $response[20]["id"]=20;
        $response[21]["role"]=" ADMINISTRADOR CLIENTE";
        $response[21]["id"]=21;
        $response[22]["role"]="DIRECTOR DE DESARROLLO";
        $response[22]["id"]=22;
        $response[23]["role"]="DESARROLLADOR";
        $response[23]["id"]=23;
        return $response;
    }

    public function translateRole($role)
    {
        switch($role){
            case 0:
                return 'ROLE_ADMIN';
                break;
            case 1:
                return 'ROLE_EXTRANET_ADMIN';
                break;
            case 2:
                return 'ROLE_EXTRANET_USER';
                break;
            case 3:
                return 'ROLE_TEMPOWNER';
                break;
            case 4:
                return 'ROLE_WEB';
                break;
            case 5:
                return 'ROLE_COMMERCIAL';
                break;
            case 6:
                return 'ROLE_AAVV'; /**ROL AGENCIA VIAJERO*/
                break;
            case 7:
                return 'ROLE_DIR_COMMERCIAL'; /** ROL DIRECTOR COMERCIAL */
                break;
            case 8:
                return 'ROLE_SALES_EXEC'; /** ROL EJECUTIVO DE VENTA */
                break;
            case 9:
                return 'ROLE_TELEMARKETING'; /** ROL TELEMARKETING */
                break;
            case 10:
                return 'ROLE_CALL_CENTER'; /** ROL ATENCION AL CLIENTE */
                break;
            case 11:
                return 'ROLE_DIR_FINANCIAL'; /** ROL DIRECTOR FINANCIERO */
                break;
            case 12:
                return 'ROLE_ACCOUNTING'; /** ROL CONTABILIDAD */
                break;
            case 13:
                return 'ROLE_HHRR'; /** ROL  RECURSOS HUMANO */
                break;
            case 14:
                return 'ROLE_FINANCIAL_ASSISTANT'; /** ROL ASISTENTE ADMINISTRATIVO  */
                break;
            case 15:
                return 'ROLE_ADMIN'; /** ROL SUPER ADMIN */
                break;
            case 16:
                return 'ROLE_DIR_GENERAL'; /** ROL   DIRECTOR GENERAL*/
                break;
            case 17:
                return 'ROLE_DIR_MARKETING'; /** ROL DIRECTOR MARKETING */
                break;
            case 18:
                return 'ROLE_MANAGER_SOCIAL'; /** ROL SOCIAL MANAGER */
                break;
            case 19:
                return 'ROLE_MANAGER_CONTENT'; /** ROL CONTENT MANAGER*/
                break;
            case 20:
                return 'ROLE_MARKETING_ONLINE '; /** ROL Marketing online */
                break;
            case 21:
                return 'ROLE_WEB'; /** ROL NVC CLIENTE */
                break;
            case 22:
                return 'DIR_ROLE_DEVELOPER'; /** ROL DIRECTOR DE DESARROLLO*/
                break;
            case 23:
                return 'ROLE_DEVELOPER'; /** ROL   DESARROLLADOR*/
                break;
            default:
                return 'ROLE_WEB';
                break;
        }
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
     * Set owner_profile
     *
     * @param OwnerProfile $ownerProfile
     *
     * @return User
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
     * Set temp_owner
     *
     * @param TempOwner $tempOwner
     *
     * @return User
     */
    public function setTempOwner(TempOwner $tempOwner = null)
    {
        $this->temp_owner = $tempOwner;

        return $this;
    }

    /**
     * Get temp_owner
     *
     * @return TempOwner
     */
    public function getTempOwner()
    {
        return $this->temp_owner;
    }

    /**
     * Set reservation_change_history
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $reservationChangeHistory
     * @return User
     */
    public function setReservationChangeHistory(\Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $reservationChangeHistory = null)
    {
        $this->reservation_change_history = $reservationChangeHistory;

        return $this;
    }

    /**
     * Get reservation_change_history
     *
     * @return \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory 
     */
    public function getReservationChangeHistory()
    {
        return $this->reservation_change_history;
    }

    /**
     * Set client_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clientProfile
     * @return User
     */
    public function setClientProfile(\Navicu\Core\Domain\Model\Entity\ClientProfile $clientProfile = null)
    {
        $this->client_profile = $clientProfile;

        return $this;
    }

    /**
     * Get client_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\ClientProfile 
     */
    public function getClientProfile()
    {
        return $this->client_profile;
    }

    /**
     * Set commercial_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\CommercialProfile $commercialProfile
     * @return User
     */
    public function setCommercialProfile(\Navicu\Core\Domain\Model\Entity\CommercialProfile $commercialProfile = null)
    {
        $this->commercial_profile = $commercialProfile;

        return $this;
    }

    /**
     * Get commercial_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\CommercialProfile 
     */
    public function getCommercialProfile()
    {
        return $this->commercial_profile;
    }

    /**
     * Add notification_output
     *
     * @param \Navicu\Core\Domain\Model\Entity\Notification $notificationOutput
     * @return User
     */
    public function addNotificationOutput(\Navicu\Core\Domain\Model\Entity\Notification $notificationOutput)
    {
        $this->notification_output[] = $notificationOutput;

        return $this;
    }

    /**
     * Remove notification_output
     *
     * @param \Navicu\Core\Domain\Model\Entity\Notification $notificationOutput
     */
    public function removeNotificationOutput(\Navicu\Core\Domain\Model\Entity\Notification $notificationOutput)
    {
        $this->notification_output->removeElement($notificationOutput);
    }

    /**
     * Get notification_output
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationOutput()
    {
        return $this->notification_output;
    }

    /**
     * Add notification_input
     *
     * @param \Navicu\Core\Domain\Model\Entity\Notification $notificationInput
     * @return User
     */
    public function addNotificationInput(\Navicu\Core\Domain\Model\Entity\Notification $notificationInput)
    {
        $this->notification_input[] = $notificationInput;

        return $this;
    }

    /**
     * Remove notification_input
     *
     * @param \Navicu\Core\Domain\Model\Entity\Notification $notificationInput
     */
    public function removeNotificationInput(\Navicu\Core\Domain\Model\Entity\Notification $notificationInput)
    {
        $this->notification_input->removeElement($notificationInput);
    }

    /**
     * Get notification_input
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationInput()
    {
        return $this->notification_input;
    }

    /**
     * Set logs_users
     *
     * @param \Navicu\Core\Domain\Model\Entity\LogsUser $logsUsers
     * @return User
     */
    public function setLogsUsers(\Navicu\Core\Domain\Model\Entity\LogsUser $logsUsers = null)
    {
        $this->logs_users = $logsUsers;

        return $this;
    }

    /**
     * Get logs_users
     *
     * @return \Navicu\Core\Domain\Model\Entity\LogsUser 
     */
    public function getLogsUsers()
    {
        return $this->logs_users;
    }

    /**
     * Set nvc_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile
     * @return User
     */
    public function setNvcProfile(\Navicu\Core\Domain\Model\Entity\NvcProfile $nvcProfile = null)
    {
        $this->nvc_profile = $nvcProfile;

        return $this;
    }

    /**
     * Get nvc_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\NvcProfile 
     */
    public function getNvcProfile()
    {
        return $this->nvc_profile;
    }

    /**
     * Set aavv_profile
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile
     * @return User
     */
    public function setAavvProfile(\Navicu\Core\Domain\Model\Entity\AAVVProfile $aavvProfile = null)
    {
        $this->aavv_profile = $aavvProfile;

        return $this;
    }

    /**
     * Get aavv_profile
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVProfile 
     */
    public function getAavvProfile()
    {
        return $this->aavv_profile;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return User
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param integer $updatedBy
     * @return User
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Get role
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRole()
    {
        return $this->role;
    }

    public function hasRole($role)
    {
        $roles = $this->getRoles();

        if(in_array($role, $roles))
            return true;
        else
            return false;
    }

    /**
     * Set subdomain
     *
     * @param \Navicu\InfrastructureBundle\Entity\Subdomain $subdomain
     * @return User
     */
    public function setSubdomain(\Navicu\InfrastructureBundle\Entity\Subdomain $subdomain = null)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain
     *
     * @return \Navicu\InfrastructureBundle\Entity\Subdomain 
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set disable_advance
     *
     * @param boolean $disableAdvance
     * @return User
     */
    public function setDisableAdvance($disableAdvance)
    {
        $this->disable_advance = $disableAdvance;

        return $this;
    }

    /**
     * Get disable_advance
     *
     * @return boolean 
     */
    public function getDisableAdvance()
    {
        return $this->disable_advance;
    }

    /**
     * Add advance_deactivations
     *
     * @param \Navicu\InfrastructureBundle\Entity\DisableAdvance $advanceDeactivations
     * @return User
     */
    public function addAdvanceDeactivation(\Navicu\InfrastructureBundle\Entity\DisableAdvance $advanceDeactivations)
    {
        $this->advance_deactivations[] = $advanceDeactivations;

        return $this;
    }

    /**
     * Remove advance_deactivations
     *
     * @param \Navicu\InfrastructureBundle\Entity\DisableAdvance $advanceDeactivations
     */
    public function removeAdvanceDeactivation(\Navicu\InfrastructureBundle\Entity\DisableAdvance $advanceDeactivations)
    {
        $this->advance_deactivations->removeElement($advanceDeactivations);
    }

    /**
     * Get advance_deactivations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdvanceDeactivations()
    {
        return $this->advance_deactivations;
    }

    /**
     * Add deactivations_applied
     *
     * @param \Navicu\InfrastructureBundle\Entity\DisableAdvance $deactivationsApplied
     * @return User
     */
    public function addDeactivationsApplied(\Navicu\InfrastructureBundle\Entity\DisableAdvance $deactivationsApplied)
    {
        $this->deactivations_applied[] = $deactivationsApplied;

        return $this;
    }

    /**
     * Remove deactivations_applied
     *
     * @param \Navicu\InfrastructureBundle\Entity\DisableAdvance $deactivationsApplied
     */
    public function removeDeactivationsApplied(\Navicu\InfrastructureBundle\Entity\DisableAdvance $deactivationsApplied)
    {
        $this->deactivations_applied->removeElement($deactivationsApplied);
    }

    /**
     * Get deactivations_applied
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeactivationsApplied()
    {
        return $this->deactivations_applied;
    }

    /**
     * La siguiente función actualiza los
     * atributos de una clase dado un arreglo
     *
     * @param $data
     */
    public function setAtributes($data)
    {
        if (is_array($data)) {
            foreach($data as $att => $val) {
                $this->$att = $val;
            }
        }
    }
}
