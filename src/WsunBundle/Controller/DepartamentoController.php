<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Departamento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Departamento controller.
 *
 */
class DepartamentoController extends Controller
{
    /**
     * Lists all departamento entities.
     *
     */
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $departamentos = $em->getRepository('WsunBundle:Departamento')->findAll();

        return $this->render('WsunBundle:departamento:index.html.twig', array(
            'departamentos' => $departamentos,
        ));
    }

    /**
     * Creates a new departamento entity.
     *
     */
    public function newAction(Request $request)
    {
        $departamento = new Departamento();
        $form = $this->createForm('WsunBundle\Form\DepartamentoType', $departamento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $empresa = $em->getRepository('WsunBundle:Empresa')->find($form->getData()->getIdEmpresa());
            
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->from('WsunBundle:Departamento', 'dep');
            $qb->select('avg(dep.valor)');
            $qb->andWhere('dep.idEmpresa = :id_empresa');
            $qb->setParameter('id_empresa', $form->getData()->getIdEmpresa());
            $rows = $qb->getQuery()->getSingleResult();
            
                $resultado= $empresa->getLimiteOrden()-$rows[1];
                if($resultado<$form->getData()->getValor() && $form->getData()->getValor()>0){
                    $mensaje = 'El valor ingresado supera el limite asignado puede ingresar un valor <= a '.$resultado;
                    $this->session->getFlashBag()->add("status",$mensaje);
                    return $this->redirectToRoute('admin_departamento_new');
                }
            $em = $this->getDoctrine()->getManager();
            $em->persist($departamento);
            $em->flush();

            return $this->redirectToRoute('admin_departamento_show', array('id' => $departamento->getId()));
        }

        return $this->render('WsunBundle:departamento:new.html.twig', array(
            'departamento' => $departamento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a departamento entity.
     *
     */
    public function showAction(Departamento $departamento)
    {
        $deleteForm = $this->createDeleteForm($departamento);

        return $this->render('WsunBundle:departamento:show.html.twig', array(
            'departamento' => $departamento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing departamento entity.
     *
     */
    public function editAction(Request $request, Departamento $departamento)
    {
        $deleteForm = $this->createDeleteForm($departamento);
        $editForm = $this->createForm('WsunBundle\Form\DepartamentoType', $departamento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
           
            
            $em = $this->getDoctrine()->getManager();
            $empresa = $em->getRepository('WsunBundle:Empresa')->find($editForm->getData()->getIdEmpresa());
            
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->from('WsunBundle:Departamento', 'dep');
            $qb->select('avg(dep.valor)');
            $qb->andWhere('dep.idEmpresa = :id_empresa');
            $qb->setParameter('id_empresa', $editForm->getData()->getIdEmpresa());
            $rows = $qb->getQuery()->getSingleResult();
            
                $resultado= $empresa->getLimiteOrden()-$rows[1];
                if($resultado<$editForm->getData()->getValor() && $editForm->getData()->getValor()>0){
                    $mensaje = 'El valor ingresado supera el limite asignado puede ingresar un valor <= a '.$resultado;
                    $this->session->getFlashBag()->add("status",$mensaje);
                    return $this->redirectToRoute('admin_departamento_edit', array('id' => $departamento->getId()));
                }
             $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_departamento_show', array('id' => $departamento->getId()));
        }

        return $this->render('WsunBundle:departamento:edit.html.twig', array(
            'departamento' => $departamento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a departamento entity.
     *
     */
    public function deleteAction(Request $request, Departamento $departamento)
    {
        $form = $this->createDeleteForm($departamento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($departamento);
            $em->flush();
        }

        return $this->redirectToRoute('admin_departamento_index');
    }

    /**
     * Creates a form to delete a departamento entity.
     *
     * @param Departamento $departamento The departamento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Departamento $departamento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_departamento_delete', array('id' => $departamento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
