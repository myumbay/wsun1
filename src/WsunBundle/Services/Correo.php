<?php
namespace WsunBundle\Services;
use Swift_Attachment;

class Correo {

    private $remitente = null;
    private $remitente_nombre = null;

    /**
     * @var \Symfony\Component\DependencyInjection\Tests\ProjectContainer 
     */
    private $containers = null;

    /**
     * @var \Swift_Mailer
     */
    private $mailer = null;

    /**
     * @var \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    private $twig = null;

    public function __construct($containers) {
        $this->containers = $containers;
        $this->mailer = $containers->get('mailer');
        $this->twig = $containers->get('templating');
        $this->remitente = $this->containers->getParameter('correo_remitente');
        $this->remitente = explode(':', $this->remitente);
        $this->remitente_nombre = isset($this->remitente[1]) ? $this->remitente[0] : '';
        //$this->remitente = $this->remitente[1];
    }
   public function enviarOrden($correo,$codigo) {
  
        $mail = \Swift_Message::newInstance();
        $mail->setTo($correo);
        $mail->setSubject('GENERACION DE ORDEN');
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody("sdsd");
     
        $this->mailer->send($mail);
    }	
 public function aceptarOrden($correo,$codigo) {
        
        $mail = \Swift_Message::newInstance();
        //$mail->addBcc($correo);
        $mail->setTo($this->remitente);
        $mail->setSubject('ORDEN APROBADA');
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody("De: ".$correo." Mensaje: Se ha aprobado la orden N° ".$codigo);
        $this->mailer->send($mail);
    }
	
    public function enviarPrueba($correo) {
        $mail = \Swift_Message::newInstance();
        $mail->setTo($correo);
        $mail->setSubject('PRUEBA SERCOP CATALOGO');
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody('PRUEBA');
        $this->mailer->send($mail);
    }
   public function enviarContacto($correo,$asunto,$detalle) {
        
        $mail = \Swift_Message::newInstance();
        //$mail->addBcc($correo);
        $mail->setTo($this->remitente);
        $mail->setSubject($asunto);
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody("De: ".$correo." Mensaje: ".$detalle);
        $this->mailer->send($mail);
    }
    public function extensionConvenio($correosProveedores, $codigoConvenio, $nombreConvenio, $fechaNueva) {
        $mail = \Swift_Message::newInstance();
        foreach ($correosProveedores as $correosProveedor) {
            $mail->addBcc($correosProveedor);
        }
        $mail->setTo($this->remitente);
        $mail->setSubject('Extensión de convenio');
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:extension_convenio.html.twig', array('codigoConvenio' => $codigoConvenio, 'nombreConvenio' => $nombreConvenio, 'fechaNueva' => $fechaNueva)
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }

    public function nuevaOrden($rucProveedor, $nombreProveedor, $correoProveedor, $codOrden,$filename) {
        $mail = \Swift_Message::newInstance();
       /* foreach ($correoProveedor as $correo) {
            $mail->addTo($correo);
        } */
		//var_dump(\Swift_Attachment::fromPath("/..prueba.pdf"));die;
		$mail->setTo($correoProveedor);
        $mail->setSubject("Se ha generado una nueva orden a su nombre ({$codOrden})");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'WsunBundle:correo:nueva_orden.html.twig', array('rucProveedor' => $rucProveedor, 'nombreProveedor' => $nombreProveedor, 'codOrden' => $codOrden)
                ), 'text/html'
        );
		$mail->attach(\Swift_Attachment::fromPath($filename));
        $this->mailer->send($mail);
    }

    public function nuevaOrdenMO($rucProveedor, $nombreProveedor, $correoProveedor, $codOrden, $fecha) {
        $mail = \Swift_Message::newInstance();
        foreach ($correoProveedor as $correo) {
            $mail->addTo($correo);
        } 
        //$mail->setTo($correoProveedor);
        $mail->setSubject("Se ha generado una nueva orden a su nombre ({$codOrden})");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:nueva_orden_mo.html.twig', array('rucProveedor' => $rucProveedor, 'nombreProveedor' => $nombreProveedor, 'codOrden' => $codOrden, 'fecha' => $fecha)
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }

    public function nuevaOrdenGC($rucProveedor, $nombreProveedor, $correoProveedor, $codOrden, $fecha) {
        $mail = \Swift_Message::newInstance();
        $mail->setTo($correoProveedor);
        $mail->setSubject("Se ha generado una nueva orden a su nombre ({$codOrden})");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:nueva_orden_gc.html.twig', array('rucProveedor' => $rucProveedor, 'nombreProveedor' => $nombreProveedor, 'codOrden' => $codOrden, 'fecha' => $fecha)
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }

    public function nuevaOrdenMantenimiento($rucProveedor, $nombreProveedor, $correoProveedor, $codOrden, $rucEntidad, $nombreEntidad, $nombreProducto, $serialProducto, $idProducto) {
        $mail = \Swift_Message::newInstance();
        $mail->setTo($correoProveedor);
        $mail->setSubject("Se ha generado una nueva orden de mantenimiento a su nombre ({$codOrden})");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:nueva_orden_mantenimiento.html.twig', array(
                    'rucProveedor' => $rucProveedor,
                    'nombreProveedor' => $nombreProveedor,
                    'codOrden' => $codOrden,
                    'rucEntidad' => $rucEntidad,
                    'nombreEntidad' => $nombreEntidad,
                    'nombreProducto' => $nombreProducto,
                    'serialProducto' => $serialProducto,
                    'idProducto' => $idProducto,
                        )
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }

    public function nuevaOrdenReposicionTemporal($rucProveedor, $nombreProveedor, $correoProveedor, $codOrden, $rucEntidad, $nombreEntidad, $nombreProducto, $serialProducto, $idProducto, $ndias, $observacion) {
        $mail = \Swift_Message::newInstance();
        $mail->setTo($correoProveedor);
        $mail->setSubject("Se ha generado una nueva solicitud de reposición temporal a su nombre ({$codOrden})");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:nueva_orden_reposicion.html.twig', array(
                    'rucProveedor' => $rucProveedor,
                    'nombreProveedor' => $nombreProveedor,
                    'codOrden' => $codOrden,
                    'rucEntidad' => $rucEntidad,
                    'nombreEntidad' => $nombreEntidad,
                    'nombreProducto' => $nombreProducto,
                    'serialProducto' => $serialProducto,
                    'idProducto' => $idProducto,
                    'ndias' => $ndias,
                    'observacion' => $observacion,
                        )
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }

    public function pujaGranCompra($correosProveedores, $codigoOrden, $items, $fechaHoraPuja, $duracion, $cantidad, $variacion) {
        $mail = \Swift_Message::newInstance();
        foreach ($correosProveedores as $correosProveedor) {
            $mail->addBcc($correosProveedor);
        }
        $mail->setTo($this->remitente);
        $mail->setSubject('Puja de gran compra - '.$codigoOrden);
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:puja_gran_compra.html.twig', array('codigoOrden' => $codigoOrden, 'items' => $items, 'fechaHoraPuja' => $fechaHoraPuja, 
                            'duracion' => $duracion, 'cantidad' => $cantidad, 'variacion' => $variacion)
                ), 'text/html'
        );
        $this->mailer->send($mail);
    }
    
    public function mensajeMedicamento($correos,$datosOrden, $ficha, $estadoOrden) {
        $mail = \Swift_Message::newInstance();
        foreach ($correos as $correo) {
            $mail->addTo($correo);
        }    
        
        $mail->setSubject("Notificación orden de compra".($estadoOrden=='1'?"":($estadoOrden=='0'?" Sin efecto":" Liquidada"))." - Repertorio de medicamentos");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
                        'SercopComunBundle:Correo:mensaje_sicm.html.twig'
                ,array('datosOrden'=>$datosOrden,'ficha'=>$ficha,'estadoOrden'=>$estadoOrden)), 'text/html'
        );
        $this->mailer->send($mail);        
    }

    /**
     * Plantilla email, Entregas Parciales - Medicamentos
     */

    public function mensajeEntregaParcialMedicamento($correos,$cc_correos,$datosOrden, $mensaje, $estadoOrden) {
        $mail = \Swift_Message::newInstance();
        foreach ($correos as $correo) {
            $mail->addTo($correo);
        }
        
        foreach ($cc_correos as $copiacorreo) {
            $mail->addCc($copiacorreo);
        }

        $mail->setSubject("Notificación orden de compra Entregas Parciales - Repertorio de medicamentos");
        $mail->setFrom($this->remitente, $this->remitente_nombre);
        $mail->setBody($this->twig->render(
            'SercopComunBundle:Correo:plantilla_correo_entpar_sicm.html.twig'
            ,array('datosOrden'=>$datosOrden,'mensaje'=>$mensaje,'estadoOrden'=>$estadoOrden)), 'text/html'
        );
        $this->mailer->send($mail);
    }
    

}
