<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parametro
 *
 * @ORM\Table(name="parametro", indexes={@ORM\Index(name="fk_pa_idx", columns={"id"}),@ORM\Index(name="fk_co_valorx", columns={"valor"}),@ORM\Index(name="fk_co_descripcionx", columns={"descripcion"})})
 * @ORM\Entity
 */
class Parametro 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
      /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float",  nullable=false, options={"comment" = "valores"})
     */
    protected $valor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=100, nullable=false, options={"comment" = "Descripción del parametro"})
     */
    protected $descripcion;
    
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Parametro
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set valor
     *
     * @param \DateTime $valor
     * @return Parametro
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }
    
   
}
