<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\CreateUserOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando que obtiene los datos de un usuario owner
 * dado el id del usuario fos_user
 *
 * @author Freddy Contreras
 */
class CreateUserOwnerCommand extends CommandBase implements Command
{
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
     * @var string Teléfono
     */
    protected $cell_phone;

    /**
     * @var contraseña del usuario a registrar
     */
    protected $password;
}