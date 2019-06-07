<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Enterprise;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Traits\FormErrorValidator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
/**
 * Enterprise controller.
 *
 * @Route("enterprise")
 */
class EnterpriseController extends Controller
{
    use FormErrorValidator;

    /**
     * @Rest\Get("/", name="enterprise_index")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $enterprises = $em->getRepository('AppBundle:Enterprise')->findAll();
        $enterprises = $this->get('jms_serializer')->serialize($enterprises, 'json', SerializationContext::create()->setGroups(array('enterprise_index')));
        return new Response($enterprises);
    }

    /**
     * @Rest\Post("/new", name="enterprise_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);
        $enterprise = new Enterprise();
        $form = $this->createForm('AppBundle\Form\EnterpriseType', $enterprise);
        $form->submit($data);

        if(!$form->isValid()) {
            $errors = $this->getErrors($form);
            $validation = [
                'type' => 'validation',
                'description' => 'data validation',
                'code'=> 400,
                'errors' => $errors
            ];
            return new JsonResponse($validation);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($enterprise);
        $em->flush();
        $enterprise = $this->get('jms_serializer')->serialize($enterprise, 'json', SerializationContext::create()->setGroups(array('enterprise_index')));
        return new Response($enterprise);
    }

    /**
     * @Rest\Get("/{id}", name="enterprise_show")
     * @param $id
     * @return Response
     */
    public function showAction(int $id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('AppBundle:Enterprise')->find($id);
        if (!$enterprise) {
            throw new HttpException(400, "Enterprise dont exist");
        }
        $enterprise = $this->get('jms_serializer')->serialize($enterprise, 'json', SerializationContext::create()->setGroups(array('enterprise_index')));
        return new Response($enterprise);
    }

     /**
     * @Rest\Put("/edit/{id}", name="enterprise_put")
     * @param Request $request
     * @return Response
     * @param $id
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('AppBundle:Enterprise')->findOneById($id);
        if (!$enterprise) {
            throw new HttpException(400, "Enterprise dont exist");
        }
        $data = json_decode($request->getContent(),true);
        $editForm = $this->createForm('AppBundle\Form\EnterpriseType', $enterprise);
        $editForm->submit($data);
        if(!$editForm->isValid()) {
            $errors = $this->getErrors($editForm);
            $validation = [
                'type' => 'validation',
                'description' => 'data validation',
                'code'=> 400,
                'errors' => $errors
            ];
            return new JsonResponse($validation);
        }
        $this->getDoctrine()->getManager()->flush();
        $enterprise = $this->get('jms_serializer')->serialize($enterprise, 'json', SerializationContext::create()->setGroups(array('enterprise_index')));
        return new Response($enterprise);
    }

    /**
     * @Rest\Delete("/remove/{id}", name="enterprise_delete")
     * @param $id
     * @return Response
     *
     */
    public function deleteAction($id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('AppBundle:Enterprise')->find($id);
        if (!$enterprise) {
            throw new HttpException(400, "Enterprise dont exist");
        }
        $em->remove($enterprise);
        $em->flush();
        $message = [
            'type' => 'Delete',
            'description' => 'Enterprise deleted',
            'code'=> 200,
        ];
        return new JsonResponse($message,200);
    }
}
