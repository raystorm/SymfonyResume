<?php

namespace Tburton\ResumeBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Tburton\ResumeBundle\Document\ResumeDocument;

/**
 *  Repository(DAO) class for marshalling my {@link resumeDocuments}
 *  in and out of Mongo
 */
class ResumeDocumentDAO extends DocumentRepository
{
  /**
   * getDocument By it's Extension
   *
   * @param $ext file extension String to search for
   * @return ResumeDocument
   */
  public function findByExt($ext)
  {
    return $this->createQueryBuilder()      //should only be 1
                 ->field('ext')->equals($ext)->limit(1)
                 ->getQuery()->getSingleResult();
  }
  
  /**
   * remove a single document by its extension
   * @param string $ext
   */
  public function removeByExt($ext)
  {
     $doc = $this->findByExt($ext);
     $this->getDocumentManager()->remove($doc);
  }
  
  /**
   *  Remove all documents with a specified extension
   *  @param string $ext
   */
  public function removeAllByExt($ext)
  {
     $iter = $this->createQueryBuilder()
                 ->field('ext')->equals($ext)
                 ->getQuery()->getIterator();
     foreach($iter as $doc) { $this->getDocumentManager()->remove($doc); }
  }
} 

?>