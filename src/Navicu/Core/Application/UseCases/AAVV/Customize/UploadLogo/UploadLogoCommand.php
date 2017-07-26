<?php
namespace Navicu\Core\Application\UseCases\AAVV\Customize\UploadLogo;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Carga de Logo personalizado de la agencia de viajes
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 22/09/2016
 */
class UploadLogoCommand extends CommandBase implements Command
{
    /**
     * @var File Archivo a carga como logo
     */
    protected $file;

    /**
     * @var string nombre del archivo
     */
    protected $nameImage;
}
