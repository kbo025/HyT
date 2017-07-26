<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\UploadDocument;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente clase es el commando del caso de uso de UploadDocument
 *
 * Class UploadDocumentCommand
 * @package Navicu\Core\Application\UseCases\AAVV\UploadDocument
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 02/09/2016
 */
class UploadDocumentCommand implements Command
{
	/**
     * @var Slug de la AAVV
     */
    private $slug;


    /**
     * @var tipo de galería habitación o otherGalleries
     */
    private $documentType;

    /**
     * @var el archivo a subir
     */
    private $file;

    private $originalName;

    public function __construct($data = null)
    {
        
        if(isset($data['slug'])){
            $this->slug = $data['slug'];
        }

        if(isset($data['documentType'])){
            $this->documentType = $data['documentType'];
        }

        if(isset($data['originalName'])){
            $this->originalName = $data['originalName'];
        }

    }

    public function getRequest()
    {
        return
            array(
                'slug' => $this->slug,
                'documentType' => $this->documentType,
                'file' => $this->file,
                'originalName' => $this->originalName
        
            );
    }

    /**
     * @return el
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @param el $file
     */
    public function setOriginalName($name)
    {
        $this->originalName = $name;
    }

    /**
     * @return el
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param el $file
     */
    public function setFile($file)
    {
        $this->file = $file;
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
     * @return tipo
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param tipo $typeGallery
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

}