<?php

namespace Tburton\ResumeBundle\Users;


use Fp\OpenIdBundle\Model\IdentityInterface;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;

/**
 *  Identity Manager class "in memory" Identity Manager
 *
 *  User: tfburton
 */
class OpenIdIdentityManager implements  IdentityManagerInterface
{
   protected $identityClass;

   function __construct($identityClass)
   { $this->identityClass = $identityClass; }

   /**
    *  @param string $identity
    *
    *  @return \Fp\OpenIdBundle\Model\IdentityInterface|null
    */
   function findByIdentity($identity)
   {
     return null;
     //consider updating to manually create a user from identity attributes
   }

   /**
    *  @return \Fp\OpenIdBundle\Model\IdentityInterface
    */
   function create() { return $this->identityClass; }

   /**
    *  @param \Fp\OpenIdBundle\Model\IdentityInterface $openIdIdentity
    *
    *  @return void
    */
   function update(IdentityInterface $identity)
   { /* no persistent storage to update */ }

   /**
    *  @param \Fp\OpenIdBundle\Model\IdentityInterface $openIdIdentity
    *
    *  @return void
    */
   function delete(IdentityInterface $identity)
   { /* no persistent storage to clear */ }

}