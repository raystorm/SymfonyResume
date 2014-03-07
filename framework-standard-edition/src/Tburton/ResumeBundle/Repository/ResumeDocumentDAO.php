<?php

namespace Tburton\ResumeBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 *  Repository(DAO) class for marshalling my {@link resumeDocuments}
 *  in and out of Mongo
 *
 */
class ResumeDocumentDAO extends DocumentRepository
{
  /**
   * getDocument By it's Extension
   * @param $ext file extension String to search for
   * @return mixed
   */
  public function findByExt($ext)
  {
    return $this->createQueryBuilder()      //should only be 1
                ->field('ext')->equals($ext)->limit(1)
                ->getQuery()->execute();
  }

} 