<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pedido
 *
 * @ORM\Table(name="pedido", indexes={@ORM\Index(name="IDX_C4EC16CEFCF8192D", columns={"id_usuario"})})
 * @ORM\Entity
 */
class Pedido
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="pedido_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_pedido", type="string", length=250, nullable=true)
     */
    private $codigoPedido;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado_pedido", type="boolean", nullable=true)
     */
    private $estadoPedido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="orden_compra", type="string", length=180, nullable=true)
     */
    private $ordenCompra;
    
 /**
     * @var integer
     *
     * @ORM\Column(name="updated_by", type="integer", nullable=false, options={"comment" = "Id del usuario del usuario que actualizó"})
     */
    protected $updatedBy;

    /**
     * @var float
     *
     * @ORM\Column(name="orden_sap", type="text",  nullable=true)
     */
    private $ordenSap;
    
      /**
     * @var float
     *
     * @ORM\Column(name="tipo_credito", type="text",  nullable=true)
     */
    private $tipoCredito;
    
     /**
     * @var float
     *
     * @ORM\Column(name="observaciones", type="text",  nullable=true)
     */
    private $observaciones;
    
     /**
    * @ORM\ManyToOne(targetEntity="Usuarios", inversedBy="Pedido")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
    */
    
    private $idUsuario;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=false, options={"comment" = "Fecha y hora de creación"})
     */
    protected $fechaCreacion;
  

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
     * Set codigoPedido
     *
     * @param string $codigoPedido
     *
     * @return Pedido
     */
    public function setCodigoPedido($codigoPedido)
    {
        $this->codigoPedido = $codigoPedido;

        return $this;
    }

    /**
     * Get codigoPedido
     *
     * @return string
     */
    public function getCodigoPedido()
    {
        return $this->codigoPedido;
    }

    
    /**
     * Set tipoCredito
     *
     * @param string $tipoCredito
     *
     * @return Pedido
     */
    public function setTipoCredito($tipoCredito)
    {
        $this->tipoCredito = $tipoCredito;

        return $this;
    }

    /**
     * Get tipoCredito
     *
     * @return string
     */
    public function getTipoCredito()
    {
        return $this->tipoCredito;
    }
    
    
    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Pedido
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
    
    /**
     * Set estadoPedido
     *
     * @param boolean $estadoPedido
     *
     * @return Pedido
     */
    public function setEstadoPedido($estadoPedido)
    {
        $this->estadoPedido = $estadoPedido;

        return $this;
    }

    /**
     * Get estadoPedido
     *
     * @return boolean
     */
    public function getEstadoPedido()
    {
        return $this->estadoPedido;
    }

    /**
     * Set updatedBy
     *
     * @param integer $updatedBy
     * @return Pedido
     */
    public function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer 
     */
    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    /**
     * Set fechaCreacion
     *
     * @param float $fechaCreacion
     *
     * @return Pedido
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return float
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set idUsuario
     *
     * @param \WsunBundle\Entity\Usuarios $idUsuario
     *
     * @return Pedido
     */
    public function setIdUsuario(\WsunBundle\Entity\Usuarios $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return \WsunBundle\Entity\Usuarios
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    
    /**
     * Set ordenSap
     *
     * @param string $ordenSap
     *
     * @return Pedido
     */
    public function setOrdenSap($ordenSap)
    {
        $this->ordenSap = $ordenSap;

        return $this;
    }
    /**
     * Get ordenSap
     *
     * @return string
     */
    public function getOrdenSap()
    {
        return $this->ordenSap;
    }
    
    /**
     * Set ordenCompra
     *
     * @param string $ordenCompra
     *
     * @return Pedido
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
    public function __construct() {
        //$this->fechaCreacion=new \DateTime;
    }
//       public function __toString() {
//        return $this->id;
//    }

    
}
