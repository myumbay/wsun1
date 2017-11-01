<?php

namespace WsunBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ubicacion
 *
 * @ORM\Table(name="parametroDepartamento")
 * @ORM\Entity
 */
class ParametroDepartamento
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
     * @ORM\Column(name="nombre_departamento", type="string", length=255, nullable=false)
     */
    private $nombreDepartamento;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=false)
     */
    private $observaciones;

    
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
     * @param string $nombreDepartamento
     *
     * @return ParametroDepartamento
     */
    public function setNombreDepartamento($nombreDepartamento)
    {
        $this->nombreDepartamento = $nombreDepartamento;

        return $this;
    }

    /**
     * Get nombreDepartamento
     *
     * @return string
     */
    public function getNombreDepartamento()
    {
        return $this->nombreDepartamento;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return NombreDepartamento
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

    public function __toString() {
        return $this->nombreDepartamento;
    }
}
