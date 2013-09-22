<?php
/**
 *   Actual User class (parsed from passed attributes)
 *
 *   User: tfburton
 */

namespace Tburton\ResumeBundle\Users;

use Monolog\Logger;
use Symfony\Component\Security\Core\User\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class OpenIdUser implements UserInterface
{
  private $userName;
  private $roles = array();

//  function __construct(array $attributes)
//  {
//     if ( null != $attributes )
//     {
//       $userName = $attributes['userName'];
//     }
//  }


  /**
   *  Returns the username used to authenticate the user.
   *
   *  @return string The username
   */
  public function getUsername()
  {
     return $this->userName;
  }

  function setFromAttributes(array $attributes)
  {
     //TODO: find out common attributes and set properties here
  }

  /**
   * Returns the roles granted to the user.
   *
   * <code>
   *   public function getRoles() { return array('ROLE_USER'); }
   * </code>
   *
   * Alternatively, the roles might be stored on a ``roles`` property,
   * and populated in any number of different ways when the user object
   * is created.
   *
   * @return Role[] The user roles
   */
  public function getRoles()
  {
    // TODO: Implement getRoles() method.
    return $this->roles;
  }

  /**
   *  Returns the password used to authenticate the user.
   *
   *  This should be the encoded password. On authentication,
   *  a plain-text password will be salted, encoded,
   *  and then compared to this value.
   *
   *  @return string The password
   */
  public function getPassword() { return ""; }

  /**
   * Returns the salt that was originally used to encode the password.
   *
   * This can return null if the password was not encoded using a salt.
   *
   * @return string The salt
   */
  public function getSalt() { return ""; }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   *
   * @return void
   */
  public function eraseCredentials() { }

}