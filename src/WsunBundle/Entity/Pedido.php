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
     * @ORM\GeneratedValue(strategy="SEQUENCE")
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
     * @var float
     *
     * @ORM\Column(name="iva", type="float", precision=10, scale=0, nullable=true)
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(name="total_pedido", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalPedido;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
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
     * Set iva
     *
     * @param float $iva
     *
     * @return Pedido
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set totalPedido
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
     * @param \WsunBundle\Entity\Usuario $idUsuario
     *
     * @return Pedido
     */
    public function setIdUsuario(\WsunBundle\Entity\Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return \WsunBundle\Entity\Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    
    /**
     * Set totalPedido
     *
     * @param float $totalPedido
     *
     * @return Pedido
     */
    public function setTotalPedido($totalPedido)
    {
        $this->totalPedido = $totalPedido;

        return $this;
    }
    /**
     * Get totalPedido
     *
     * @return float
     */
    public function getTotalPedido()
    {
        return $this->totalPedido;
    }
    public function __construct() {
        //$this->fechaCreacion=new \DateTime;
    }
//       public function __toString() {
//        return $this->id;
//    }

    
}
