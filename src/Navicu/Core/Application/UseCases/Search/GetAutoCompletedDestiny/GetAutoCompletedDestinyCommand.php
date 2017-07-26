<?php
namespace Navicu\Core\Application\UseCases\Search\GetAutoCompletedDestiny;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Comando hace uso del motor de busqueda para generar una lista de
 * destinos por medio de autocompletado.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetAutoCompletedDestinyCommand extends CommandBase implements Command
{
    /**
     * Variable con el conjunto de palabras a buscar.
     *
     * @var String $words
     */
    protected $words;

    /**
     * Variable para el manejo del codigo alfa del pais.
     *
     * @var String $alfa
     */
    protected $alfa;

    /**
     * Variable para incluir o no establecimientos en la busqueda.
     *
     * @var Boolean $properties
     */
    protected $properties;
}
