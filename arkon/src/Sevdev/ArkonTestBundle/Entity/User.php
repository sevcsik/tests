<?php

namespace Sevdev\ArkonTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sevdev\ArkonTestBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sevdev\ArkonTestBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var datetime $modifiedAt
     *
     * @ORM\Column(name="modifiedAt", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var smallint $role
     *
     * @ORM\Column(name="role", type="smallint")
     */
    private $role;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password (hash)
     *
     * @param string $password sha1 password hash
     */
    public function setPasswordHash($password)
    {
        $this->password = $password;
    }

    /**
     * Set password (plaintext)
     *
     * @param string $password plaintext password
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param datetime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * Get modifiedAt
     *
     * @return datetime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set role
     *
     * @param smallint $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role
     *
     * @return smallint 
     */
    public function getRole()
    {
        return $this->role;
    }

    // Additional UserInterface methods

    /**
     * Returns the roles granted to the user.
     *
     * @return array array of ROLE_* roles
     */
    public function getRoles()
    {
      $roles = array('ROLE_USER', 'ROLE_ADMIN');
      return array($roles[$this->role]);
    }

    /**
     * Returns salt (currently null)
     * @return string
     */
    public function getSalt()
    {
      return null;
    }

    /**
     * Erase sensitive data (currently nothing)
     *
     * @return void
     */
    public function eraseCredentials()
    {

    }

    /**
     * Checks if the User is equal to another
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface user
     * @return bool result
     */
    public function equals(
      \Symfony\Component\Security\Core\User\UserInterface $user
    )
    {
      return $this->username == $user->getUsername();
    }

    /**
     * Update creation date to now. Bound to \@PrePersist
     *
     * @ORM\PrePersist
     */
    public function updateCreationDate()
    {
        $this->createdAt = $this->modifiedAt = new \DateTime();
    }

    /**
     * Update modification date to now. Bound to \@PreUpdate
     *
     * @ORM\PreUpdate
     */
    public function updateModificationDate()
    {
        $this->modifiedAt = new \DateTime();
    }
}
