<?php

namespace Tburton\ResumeBundle\Users;

use Fp\OpenIdBundle\Entity\UserIdentity;
use Symfony\Component\Security\Core\User\UserInterface;

use Fp\OpenIdBundle\Model\UserIdentityInterface;

/**
 * Base User Class to be used by the User manager (no storage)
 */
class OpenIdUserIdentity extends UserIdentity
{
  /**
   * @var integer
   */
  protected $id;

  /**
   * The relation is made eager by purpose.
   * More info here: {@link https://github.com/formapro/FpOpenIdBundle/issues/54}
   *
   * @var UserInterface
   */
  protected $user;

  /**
   * It inherits an "identity" string field, and an "attributes" text field
   */
  public function __construct()
  {
    parent::__construct();
    // your own logic (nothing for this example)
  }
}
