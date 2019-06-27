<?php

namespace WsunBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
  * @ORM\Entity
  * @ORM\Table(name="usuarios")
  */
class Usuarios implements UserInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=50, nullable=true)
     */
    private $ruc;
    /**
    * @ORM\Column(type="string", length=255)
    */
    protected $username;
 
    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;
 
    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=180, nullable=true)
     */
    private $correo;
    
    /**
    * @ORM\ManyToOne(targetEntity="Departamento", inversedBy="Usuarios")
     * @ORM\JoinColumn(name="id_departamento", referencedColumnName="id")
    */
    private $departamento;
 
    /**
     * se utilizó user_roles para no hacer conflicto al aplicar ->toArray en getRoles()
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $user_roles;
 
    public function __construct()
    {
        $this->user_roles = new \Doctrine\Common\Collections\ArrayCollection();
    }
 
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
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
 
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
 
    /**
     * Add user_roles
     *
     * @param WsunBunudle\Entity\Role $userRoles
     */
    public function addRole(\WsunBundle\Entity\Role $userRoles)
    {
        $this->user_roles[] = $userRoles;
    }
 
    public function setUserRoles($roles) {
        $this->user_roles = $roles;
    }
 
    /**
     * Get user_roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUserRoles()
    {
        return $this->user_roles;
    }
 
    /**
     * Get roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->user_roles->toArray(); //IMPORTANTE: el mecanismo de seguridad de Sf2 requiere ésto como un array
    }
 
    /**
     * Compares this user to another to determine if they are the same.
     *
     * @param UserInterface $user The user
     * @return boolean True if equal, false othwerwise.
     */
    public function equals(UserInterface $user) {
        return md5($this->getUsername()) == md5($user->getUsername());
 
    }
 
    /**
     * Erases the user credentials.
     */
    public function eraseCredentials() {
 
    }
    
    /**
     * Set departamento
     *
     * @param \WsunBundle\Entity\Departamento $departamento
     *
     * @return Departamento
     */
    public function setDepartamento(\WsunBundle\Entity\Departamento $departamento = null)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get departamento
     *
     * @return \WsunBundle\Entity\Departamento
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }
    /**
     * Set ruc
     *
     * @param string $ruc
     *
     * @return Usuario
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return string
     */
    public function getRuc()
    {
        return $this->ruc;
    }
    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Usuario
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }
	   /**
     * Add userRole
     *
     * @param \WsunBundle\Entity\Role $userRole
     *
     * @return Usuarios
     */
    public function addUserRole(\WsunBundle\Entity\Role $userRole)
    {
        $this->user_roles[] = $userRole;

        return $this;
    }

    /**
     * Remove userRole
     *
     * @param \WsunBundle\Entity\Role $userRole
     */
    public function removeUserRole(\WsunBundle\Entity\Role $userRole)
    {
        $this->user_roles->removeElement($userRole);
    }
}