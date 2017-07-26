<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\EditUserOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando que obtiene los datos de un usuario admin
 * dado el id del usuario fos_user
 *
 * @author Freddy Contreras
 */
class EditUserOwnerCommand extends CommandBase implements Command
{
    protected $user_id;
    /**
     * @var integer trato del usuario
     */
    protected $treatment;

    /**
     * @var string nombre completo del perfil
     */
    protected $full_name;

    /**
     * @var string cedula de identidad
     */
    protected $identity_card;

    /**
     * @var date fecha de nacimiento
     */
    protected $birth_date;

    /**
     * @var string correo del usuario
     */
    protected $email;

    /**
     * @var string celular
     */
    protected $cell_phone;

    /**
     * @var string teléfono
     */
    protected $phone;

}