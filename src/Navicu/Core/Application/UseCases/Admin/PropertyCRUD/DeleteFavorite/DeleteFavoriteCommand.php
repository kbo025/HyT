<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteFavorite;

use Navicu\Core\Application\Contract\Command;

/**
 * La siguiente clase representa el commando
 * del caso de uso "Eliminar una imagen Favorito de un establecimiento"
 *
 * Class DeleteFavoriteCommand
 *
 * @package Navicu\Core\Application\UseCases\Admin\PropertyCrud\UploadImage
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 12/01/2015
 */
class DeleteFavoriteCommand implements Command
{
    /**
     * @var Slug del establecimiento
     */
    private $slug;

    /**
     * @var id de la Imagen Favorita
     */
    private $idFavorite;

    /**
     * @var Tipo de eliminación
     */
    private $deleteType;


    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'idFavorite' => $this->idFavorite,
                'deleteType' => $this->deleteType
            );
    }

    /**
     * @return Slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param Slug $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return id
     */
    public function getIdFavorite()
    {
        return $this->idFavorite;
    }

    /**
     * @param id $idFavorite
     */
    public function setIdFavorite($idFavorite)
    {
        $this->idFavorite = $idFavorite;
    }

    /**
     * @return Tipo
     */
    public function getDeleteType()
    {
        return $this->deleteType;
    }

    /**
     * @param Tipo $deleteType
     */
    public function setDeleteType($deleteType)
    {
        $this->deleteType = $deleteType;
    }
}