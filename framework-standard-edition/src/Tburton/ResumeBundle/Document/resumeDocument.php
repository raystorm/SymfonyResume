<?php
namespace Tburton\ResumeBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 *  for storing the various resume formats in Mongo
 *  @MongoDB\Document(repositoryClass="Tburton\ResumeBundle\Repository\ResumeDocumentDAO")
 */
class resumeDocument
{
	/**
	 * String indicating File type
	 * 
	 * @MongoDB\string
	 */
	protected $ext;
	
	/**
	 * File contents
	 * @MongoDB\file
	 */
	protected $file;

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
    public function getExt()
    {
        return $this->ext;
    }

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
    public function getFile()
    {
        return $this->file;
    }
}
