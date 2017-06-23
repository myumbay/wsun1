<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ubicacion
 *
 * @ORM\Table(name="ubicacion")
 * @ORM\Entity
 */
class Ubicacion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */ 
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=35, nullable=false)
     */
    private $pais;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=35, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=50, nullable=true)
     */
    private $detalle;



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
     * Set pais
     *
     * @param string $pais
     *
     * @return Ubicacion
     */
    public function setPais($pais)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return string
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Ubicacion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     *
     * @return Ubicacion
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }
    public function __toString() {
        return $this->detalle;
    }
}
