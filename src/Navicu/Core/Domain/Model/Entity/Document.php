<?php
namespace Navicu\Core\Domain\Model\Entity;

/**
 * La clase se encarga del manejo de archivos del sistema
 *
 * Se define una clase y una serie de propiedades para el manejo de archivos del sistema.
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 13-05-15
 */
class Document
{
    /**
     * Esta propiedad es usada como llave primaria dentro de la DB.
     * 
     * @var Integer
     */
    protected $id;
    
    /**
     * Esta propiedad representa un nombre auxiliar del documento
     * 
     * @var String
     */
    public $name;

    /**
     * Esta hace referencia al nombre del archivo 
     * con su extesión (.jpg, .png, .pdf, etc)
     * 
     * @var String
     */
    public $fileName;

    /**
     * Esta propiedad hace referencia al archivo
     * 
     */
    private $file;

    /**
     * Esta propiedad hace referencia al subDominio donde 
     * se almacenará la imagen (ej: /imagenes/.. )
     */
    private $subDir;

    /**
     * @var ArrayCollection
     */
    private $room_images_gallery;

    /**
     * @var ArrayCollection
     */
    protected $property_favorite_images;

    /**
     * @var ArrayCollection
     */
    protected $property_images_gallery;

    /**
     * constructor
     */
    public function __construct() { }

    public function setFile($file = null)
    {
      $this->file = $file;
    }

    public function getFile()
    {
      return $this->file;
    }
    
    /**
     * Set subDir
     *
     * @param string $subDir
     * @return DocumentEntity
     */ 
    public function setSubDir($subDir = null)
    {
      $this->subDir = $subDir;
    }

    /**
     * Get subDir
     *
     * @return String
     */
    public function getSubDir()
    {
      return $this->subDir;
    }
  
    /**
     * Get idgit fetch && git checkout hotfix/1.11.9
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Document
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set room_images_gallery
     *
     * @param \Navicu\Core\Domain\Model\Entity\RoomImagesGallery $roomImagesGallery
     * @return Document
     */
    public function setRoomImagesGallery(\Navicu\Core\Domain\Model\Entity\RoomImagesGallery $roomImagesGallery = null)
    {
        $this->room_images_gallery = $roomImagesGallery;

        return $this;
    }

    /**
     * Get room_images_gallery
     *
     * @return \Navicu\Core\Domain\Model\Entity\RoomImagesGallery 
     */
    public function getRoomImagesGallery()
    {
        return $this->room_images_gallery;
    }

    /**
     * Set property_favorite_images
     *
     * @param \Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages $propertyFavoriteImages
     * @return Document
     */
    public function setPropertyFavoriteImages(\Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages $propertyFavoriteImages = null)
    {
        $this->property_favorite_images = $propertyFavoriteImages;

        return $this;
    }

    /**
     * Get property_favorite_images
     *
     * @return \Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages 
     */
    public function getPropertyFavoriteImages()
    {
        return $this->property_favorite_images;
    }

    /**
    * La función retorna la dirección absoluta del archivo
    *
    * @return string
    */
    public function getAbsolutePath()
    {
      return null === $this->fileName
      ? null
      : $this->getUploadRootDir().'/'.$this->fileName;
    }

    /**
    * La función retorna la dirección archivo
    *
    * @return string
    */
    public function getWebPath()
    {
      return null === $this->fileName
      ? null
      : $this->getSubDir().'/'.$this->fileName;
    }

    /**
    * La función retorna la dirección absoluta del subdominio del archivo
    *
    * @return string
    */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
      return $_SERVER['DOCUMENT_ROOT'].$this->getSubDir();
    }
    
    /**
    * La función crea un documento en el directorio
    *
    * @param String $typeFile 
    * @param String $fileName
    * @param String $subDir
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 25/06/2015
    * @return Void
    */
    public function upload($typeFile = 'image', $subDir = '', $nameFile = null)
    {

        if (null === $this->getFile()) {
            return;
        }

        $pathTypeFile = $this->getPathTypeFile($typeFile);
        $this->setSubDir($pathTypeFile.$subDir);

        // Si no existe el directorio se crea el directorio
        if (!file_exists($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(),0775, true);
        }

        if (is_null($nameFile))
            $nameFile = substr(time() + rand(), 0, 14);
        else{
            $nameFile = $nameFile.substr(time()+rand(), 0, 14);
        }
        if($typeFile == 'aavv_image')
            $extension = 'jpg';
        elseif($typeFile == 'aavv_document' or $typeFile == 'document')
            $extension = 'pdf';
        else
            $extension = $this->getFile()->guessExtension();
        $path = $this->getUploadRootDir().$nameFile.'.'.$extension;

        $this->setFileName($subDir.$nameFile.'.'.$extension);

	    if (is_dir($this->getUploadRootDir()) && is_writable($this->getUploadRootDir())) {
		    if(($typeFile == 'aavv_image') or ($typeFile == 'aavv_document') or ($typeFile == 'document')){
			    if(!file_put_contents($path, $this->getFile()))
				    throw new \Exception('No content was written',500);
		    } else {
			    if(!move_uploaded_file($this->getFile(), $path))
				    throw new \Exception('No content was written',500);
		    }
	    } else {
		    throw new \Exception('Upload directory is not writable, or does not exist.',500); 
	    }



    }

    /**
    * La siguiente función retorna el directorio principal 
    * del documento según el tipo de documento (imagen, video, pdf, etc)
    * 
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 25/06/2015
    */
    private function getPathTypeFile($type)
    {
        $response = 'image';

        switch ($type) {
            case 'image':
                $response  = '/uploads/images/images_original/';
                break;

            case 'aavv-logo':
                $response = 'uploads/aavv/';
                break;
            case 'document':
                $response = '/uploads/documents/';
                break;
            default:
                $response  = '/uploads/images/images_original/';
                break;
        }

        return $response;
    }

    /**
     * Set property_images_gallery
     *
     * @param \Navicu\Core\Domain\Model\Entity\PropertyImagesGallery $propertyImagesGallery
     * @return Document
     */
    public function setPropertyImagesGallery(\Navicu\Core\Domain\Model\Entity\PropertyImagesGallery $propertyImagesGallery = null)
    {
        $this->property_images_gallery = $propertyImagesGallery;

        return $this;
    }

    /**
     * Get property_images_gallery
     *
     * @return \Navicu\Core\Domain\Model\Entity\PropertyImagesGallery 
     */
    public function getPropertyImagesGallery()
    {
        return $this->property_images_gallery;
    }
}
