<?php

namespace WsunBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * EmpresaProducto
 *
 * @ORM\Table(name="empresa_producto", indexes={@ORM\Index(name="fk_producto_empresa_idx", columns={"empresa_id"})})
 * @ORM\Entity
 */
class EmpresaProducto{
  /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="producto_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
/**
 * @ORM\ManyToOne(targetEntity="WsunBundle\Entity\Empresa", inversedBy="empresaProducto")
 * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
 */
private $empresa;

/**
 * @ORM\ManyToOne(targetEntity="WsunBundle\Entity\Producto", inversedBy="empresaProducto")
 * @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
 */
private $producto;

/**
     * @var float
     *
     * @ORM\Column(name="capacidad", type="float", precision=10, scale=0, nullable=true)
     */
private $capacidad;

 /**
     * @var string
     *
     * @ORM\Column(name="unidad", type="string", nullable=true, options={"comment" = "unidades de cada producto segun la empresa"})
     */
protected $unidad;

 /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=1, nullable=true, options={"comment" = "Estado del registro 1 SUb Categoria, 0 Categoria Principal"})
     */
   
protected $estado; 

/**
 * @var datetime $created
 *
 * @ORM\Column(name="created", type="datetime")
 */

private $created;
 
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
     * Set empresa
     *
     * @param integer $empresa
     * @return EmpresaProducto
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get producto
     *
     * @return integer 
     */
    public function getProducto()
    {
        return $this->producto;
    }
    /**
     * Set producto
     *
     * @param integer $producto
     * @return EmpresaProducto
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return integer 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }
    
    /**
     * Set estado
     *
     * @param string $estado
     * @return EmpresaProducto
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }
    
     /**
     * Get unidad
     *
     * @return integer 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
    
    /**
     * Set unidad
     *
     * @param string $unidad
     * @return EmpresaProducto
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }
    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return (boolean)$this->estado;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $created
     * @return EmpresaProducto
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
  /**
     * Set capacidad
     *
     * @param float $capacidad
     *
     * @return EmpresaProducto
     */
    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;

        return $this;
    }

    /**
     * Get capacidad
     *
     * @return float
     */
    public function getCapacidad()
    {
        return $this->capacidad;
    }
      public function __toString() {
        return (string)$this->producto;
   }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
