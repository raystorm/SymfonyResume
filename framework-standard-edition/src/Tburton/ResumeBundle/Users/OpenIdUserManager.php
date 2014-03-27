<?php

namespace Tburton\ResumeBundle\Users;

use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Tburton\ResumeBundle\Users\OpenIdUserIdentity;


/**
 *  Class for managing and creating the User Instances
 *
 *  User: tfburton
 */
class OpenIdUserManager extends UserManager
{
  private $adminAddress;

  // we will use an EntityManager, so inject it via constructor
  public function __construct(IdentityManagerInterface $identityManager,
                              $addresses)
                              //, EntityManager $entityManager)
  {
    parent::__construct($identityManager);
    //$this->entityManager = $entityManager;
    $this->adminAddress = $addresses;
  }

  /**
   * @param string $identity
   *  an OpenID token. With Google it looks like:
   *  https://www.google.com/accounts/o8/id?id=SOME_RANDOM_USER_ID
   * @param array $attributes
   *  requested attributes (explained later).
   * At the moment just assume there's a 'contact/email' key
   */
  public function createUserFromIdentity($identity, array $attributes = array())
  {
    // put your user creation logic here
    // what follows is a typical example

    if (false === isset($attributes['contact/email']))
    { throw new \Exception('We need your e-mail address!'); }

    //$user = new OpenIdUser();
    //$user->setAttributes($attributes);
    //$user->setIdentity($identity);

    // in this example, we fetch User entities by e-mail
    //$user = $this->entityManager->getRepository('AcmeDemoBundle:User')
    //             ->findOneBy(array('email' => $attributes['contact/email']));

    //if (null === $user)
    //{ throw new BadCredentialsException('No corresponding user!'); }

    // we create an OpenIdIdentity for this User
    //$openIdIdentity = new OpenIdIdentity();
    $openIdIdentity = new OpenIdUserIdentity();
    $openIdIdentity->setIdentity($identity);
    $openIdIdentity->setAttributes($attributes);
    //$openIdIdentity->setUser($user);

    //$this->entityManager->persist($openIdIdentity);
    //$this->entityManager->flush();

    // end of example

    $user = new OpenIdUser($this->adminAddress);
    $user->setFromAttributes($attributes);

    $openIdIdentity->setUser($user);

    return $user; // you must return a UserInterface instance (or throw an exception)
    //return $openIdIdentity;
  }

  public function refreshUser(UserInterface $user) { return $user; }

  public function supportsClass($class)
  { return $class === 'Tburton\ResumeBundle\Users\OpenIdUser'; }

}
