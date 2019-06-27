<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa", indexes={@ORM\Index(name="IDX_B8D75A50A20E254D", columns={"idubicacion"})})
 * @ORM\Entity
 */
class Empresa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="empresa_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=50, nullable=true)
     */
    private $ruc;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_emp", type="string", length=255, nullable=true)
     */
    private $nombreEmp;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono_emp", type="string", length=20, nullable=true)
     */
    private $telefonoEmp;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_emp", type="string", length=180, nullable=true)
     */
    private $direccionEmp;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="orden_compra", type="float", precision=10, scale=0, nullable=true)
     */
    private $ordenCompra;


    /**
     * @var boolean
     *
     * @ORM\Column(name="credito", type="boolean", nullable=true)
     */
    private $credito;

    /**
     * @var float
     *
     * @ORM\Column(name="limite_orden", type="float", precision=10, scale=0, nullable=true)
     */
    private $limiteOrden;
 
    /**
     * @var \Ubicacion
     *
     * @ORM\ManyToOne(targetEntity="Ubicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idubicacion", referencedColumnName="id")
     * })
     */
    private $idubicacion;
    
    /**
    * @ORM\OneToMany(targetEntity="WsunBundle\Entity\EmpresaProducto", mappedBy="empresa", cascade={"persist"})
    */
    private $empresaProducto;
    
    

    public function __construct() {
        $this->empresaProducto = new \Doctrine\Common\Collections\ArrayCollection();
    }
        public function addEmpresaProducto(EmpresaProducto $empresaProducto)
    {
        $certificate->setEmpresa($this);
        $this->empresaProducto[] = $empresaProducto;
        return $this;
    }
 
    public function removeCertificate(EmpresaProducto $empresaProducto)
    {
        $this->empresaProducto->removeElement($empresaProducto);
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
     * Set ruc
     *
     * @param string $ruc
     *
     * @return Empresa
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
     * Set nombreEmp
     *
     * @param string $nombreEmp
     *
     * @return Empresa
     */
    public function setNombreEmp($nombreEmp)
    {
        $this->nombreEmp = $nombreEmp;

        return $this;
    }

    /**
     * Get nombreEmp
     *
     * @return string
     */
    public function getNombreEmp()
    {
        return $this->nombreEmp;
    }

    /**
     * Set telefonoEmp
     *
     * @param string $telefonoEmp
     *
     * @return Empresa
     */
    public function setTelefonoEmp($telefonoEmp)
    {
        $this->telefonoEmp = $telefonoEmp;

        return $this;
    }

    /**
     * Get telefonoEmp
     *
     * @return string
     */
    public function getTelefonoEmp()
    {
        return $this->telefonoEmp;
    }

    /**
     * Set direccionEmp
     *
     * @param string $direccionEmp
     *
     * @return Empresa
     */
    public function setDireccionEmp($direccionEmp)
    {
        $this->direccionEmp = $direccionEmp;

        return $this;
    }

    /**
     * Get direccionEmp
     *
     * @return string
     */
    public function getDireccionEmp()
    {
        return $this->direccionEmp;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Empresa
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set ordenCompra
     *
     * @param string $ordenCompra
     *
     * @return Empresa
     */
    public function setOrdenCompra($ordenCompra)
    {
        $this->ordenCompra = $ordenCompra;

        return $this;
    }

    /**
     * Get ordenCompra
     *
     * @return string
     */
    public function getOrdenCompra()
    {
        return $this->ordenCompra;
    }

   

    /**
     * Set credito
     *
     * @param boolean $credito
     *
     * @return Empresa
     */
    public function setCredito($credito)
    {
        $this->credito = $credito;

        return $this;
    }

    /**
     * Get credito
     *
     * @return boolean
     */
    public function getCredito()
    {
        return $this->credito;
    }

    /**
     * Set limiteOrden
     *
     * @param float $limiteOrden
     *
     * @return Empresa
     */
    public function setLimiteOrden($limiteOrden)
    {
        $this->limiteOrden = $limiteOrden;

        return $this;
    }

    /**
     * Get limiteOrden
     *
     * @return float
     */
    public function getLimiteOrden()
    {
        return $this->limiteOrden;
    }

    /**
     * Set idubicacion
     *
     * @param \WsunBundle\Entity\Ubicacion $idubicacion
     *
     * @return Empresa
     */
    public function setIdubicacion(\WsunBundle\Entity\Ubicacion $idubicacion = null)
    {
        $this->idubicacion = $idubicacion;

        return $this;
    }

    /**
     * Get idubicacion
     *
     * @return \WsunBundle\Entity\Ubicacion
     */
    public function getIdubicacion()
    {
        return $this->idubicacion;
    }
    
    public function __toString() {
        return $this->nombreEmp;
    }
}
