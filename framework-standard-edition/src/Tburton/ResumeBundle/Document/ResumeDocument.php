<?php

namespace Tburton\ResumeBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 *  for storing the various resume formats in Mongo
 *  @MongoDB\Document(repositoryClass="Tburton\ResumeBundle\Repository\ResumeDocumentDAO")
 */
class ResumeDocument
{
    /** default constructor */
    function __construct() { }
   
    /**
     * Sets the extension and binary file data conveniently.
     *
     * @param string $ext
     * @param binary $file
     */
    public static function factory($ext, $file)
    {
      $ret = new ResumeDocument();
      $ret->setExt($ext);
      $ret->setFile($file);
   
      return $ret;
    }

	/** 
	 * File type (extension) should be the Id
	 * 
	 * //(strategy="NONE", type="string") 
	 * 
	 * @MongoDB\Id
	 */
	protected $id;
    
	/**
	 *  Extension (File Type)
	 * 
	 *  @MongoDB\String
	 */
	protected $ext;
	
	/**
	 * File contents
	 * @MongoDB\bin
	 */
	protected $file;

	/** @MongoDB\String */
	private $mimeType;

	/** @MongoDB\Date */
	private $uploadDate;

	/** @MongoDB\Int */
	private $length;

	/** @MongoDB\Int */
	private $chunkSize;

	/** @MongoDB\String */
	private $md5;

    /**
     * Set id
     *
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
       $this->id = $id;
       return $this;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId() { return $this->id; }

    /**
     * Set ext
     *
     * @param string $ext
     * @return self
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
        return $this;
    }

    /**
     * Get ext
     *
     * @return string $ext
     */
    public function getExt() { return $this->ext; }

    /**
     * Set file
     *
     * @param file $file
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return file $file
     */
    public function getFile() { return $this->file; }

	public function getMimeType() { return $this->mimeType; }

	public function setMimeType($mimeType) { $this->mimeType = $mimeType; }

	public function getChunkSize() { return $this->chunkSize;	}

	public function getLength() { return $this->length; }

	public function getMd5() { return $this->md5; }

	public function getUploadDate() { return $this->uploadDate; }
}
