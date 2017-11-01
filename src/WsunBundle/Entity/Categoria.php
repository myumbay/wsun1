<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace WsunBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of Categoria
 *
 * @author cyumbay
 */

/**
 * Categoria
 *
 * @ORM\Table(name="categoria", indexes={@ORM\Index(name="categoria_estado_idx1", columns={"id"})})
 * @ORM\Entity
 */
class Categoria 
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre_cat", type="string", length=255, nullable=false, options={"comment" = "Nombre de la categoría"})
     */
    protected $nombreCat;
  
    /**
     * @var integer
     *
     * @ORM\Column(name="padre_id", type="integer", nullable=true, options={"comment" = "Id de la categoría padre, null en caso de principal"})
     */
    protected $padreId;
  
    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=1, nullable=true, options={"comment" = "Estado del registro 1 SUb Categoria, 0 Categoria Principal"})
     */
   
    protected $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="WsunBundle\Entity\Producto", mappedBy="categoria")
     */
    protected $producto;

    /**
     * @ORM\OneToMany(targetEntity="Categoria", mappedBy="padre")
     */
    protected $hijos;

    /**
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="hijos")
     * @ORM\JoinColumn(nullable=true, name="padre_id", referencedColumnName="id")
     */
    protected $padre;
       
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->producto = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set nombreCat
     *
     * @param string $nombreCat
     * @return Categoria
     */
    public function setNombreCat($nombreCat)
    {
        $this->nombreCat = $nombreCat;

        return $this;
    }

    /**
     * Get nombreCat
     *
     * @return string 
     */
    public function getNombreCat()
    {
        return $this->nombreCat;
    }

    /**
     * Set padreId
     *
     * @param integer $padreId
     * @return Categoria
     */
    public function setPadreId($padreId)
    {
        $this->padreId = $padreId;

        return $this;
    }

    /**
     * Get padreId
     *
     * @return integer 
     */
    public function getPadreId()
    {
        return $this->padreId;
    }
    /**
     * Set estado
     *
     * @param string $estado
     * @return Categoria
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return Categoria
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Add producto
     *
     * @param \WsunBundle\Entity\Producto $producto
     * @return Categoria
     */
    public function addProducto(\WsunBundle\Entity\Producto $producto)
    {
        $this->producto[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param \WsunBundle\Entity\Producto $producto
     */
    public function removeProducto(\WsunBundle\Entity\Producto $producto)
    {
        $this->producto->removeElement($producto);
    }

    /**
     * Get producto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Add hijos
     *
     * @param \Sercop\Bundle\ComunBundle\Entity\Categoria $hijos
     * @return Categoria
     */
    public function addHijo(\WsunBundle\Entity\Categoria $hijos)
    {
        $this->hijos[] = $hijos;

        return $this;
    }

    /**
     * Remove hijos
     *
     * @param \Sercop\Bundle\ComunBundle\Entity\Categoria $hijos
     */
    public function removeHijo(\WsunBundle\Entity\Categoria $hijos)
    {
        $this->hijos->removeElement($hijos);
    }

    /**
     * Get hijos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHijos()
    {
        return $this->hijos;
    }

    /**
     * Set padre
     *
     * @param \WsunBundle\Entity\Categoria $padre
     * @return Categoria
     */
    public function setPadre(\WsunBundle\Entity\Categoria $padre = null)
    {
        $this->padre = $padre;

        return $this;
    }

    /**
     * Get padre
     *
     * @return \WsunBundle\Entity\Categoria 
     */
    public function getPadre()
    {
        return $this->padre;
    }
        /**
     * @return string
     */
     public function __toString()
     {
         return $this->nombreCat; //Esto para relacionar con producto
     }
    
}
