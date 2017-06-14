<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Producto
 *
 * @ORM\Table(name="producto", indexes={@ORM\Index(name="fk_producto_categoria1_idx", columns={"categoria_id"})})
 * @ORM\Entity
 */

class Producto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="producto_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_producto", type="string", length=50, nullable=true)
     */
    private $nombreProducto;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_producto", type="string", length=255, nullable=true)
     */
    private $codigoProducto;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_producto", type="float", precision=10, scale=0, nullable=true)
     */
    private $precioProducto;

     /**
     * @var text
     *
     * @ORM\Column(name="observacion", type="text",  options={"comment" = "Observación de la encuesta"})
     */
    
    protected $observacion;

    
    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=1, nullable=true, options={"comment" = "Estado del registro 1 activo, 0 inactivo"})
     */
    protected $estado;
     /**
     * @var \WsunBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="WsunBundle\Entity\Categoria", inversedBy="producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * })
     */
    protected $categoria;
    
    /**
    * @ORM\OneToMany(targetEntity="WsunBundle\Entity\EmpresaProducto", mappedBy="producto", cascade={"persist"})
    */
    private $empresaProducto;
    
    public function __construct() {
        $this->categoriasParaAgregar = new \Doctrine\Common\Collections\ArrayCollection();
       
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
     * Set nombreProducto
     *
     * @param string $nombreProducto
     *
     * @return Producto
     */
    public function setNombreProducto($nombreProducto)
    {
        $this->nombreProducto = $nombreProducto;

        return $this;
    }

    /**
     * Get nombreProducto
     *
     * @return string
     */
    public function getNombreProducto()
    {
        return $this->nombreProducto;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     *
     * @return Producto
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set codigoProducto
     *
     * @param string $codigoProducto
     *
     * @return Producto
     */
    public function setCodigoProducto($codigoProducto)
    {
        $this->codigoProducto = $codigoProducto;

        return $this;
    }

    /**
     * Get codigoProducto
     *
     * @return string
     */
    public function getCodigoProducto()
    {
        return $this->codigoProducto;
    }

    /**
     * Set precioProducto
     *
     * @param float $precioProducto
     *
     * @return Producto
     */
    public function setPrecioProducto($precioProducto)
    {
        $this->precioProducto = $precioProducto;

        return $this;
    }

    /**
     * Get precioProducto
     *
     * @return float
     */
    public function getPrecioProducto()
    {
        return $this->precioProducto;
    }
    
    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return Producto
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
    /**
     * Set estado
     *
     * @param string $estado
     * @return Producto
     */
    public function setEstado($estado) {
        $this->estado = $estado;

        return $this;
    }
  
    /**
     * Get estado
     *
     * @return string 
     */

    public function getEstado() {
        return (boolean)$this->estado;
//    if ($this->estado===1) {
//        return TRUE;
//    }
//    return FALSE;
    }
    
    /**
     * Set categoria
     *
     * @param \WsunBundle\Entity\Categoria $categoria
     * @return Producto
     */
    public function setCategoria(\WsunBundle\Entity\Categoria $categoria = null) {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \WsunBundle\Entity\Categoria 
     */
    public function getCategoria() {
        return $this->categoria;
    }
     public function __toString() {
        return $this->nombreProducto;
    }
}
