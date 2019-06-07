<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Developer;
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
 * Developer controller.
 *
 * @Route("developer")
 */
class DeveloperController extends Controller
{
    use FormErrorValidator;

     /**
     * @Rest\Get("/", name="developer_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $developers = $em->getRepository('AppBundle:Developer')->findAll();
        $developer = $this->get('jms_serializer')->serialize($developers, 'json', SerializationContext::create()->setGroups(array('developer_index')));
        return new Response($developer);
    }

    /**
     * @Rest\Post("/new", name="developer_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);
        $this->validateAction($data);
        $data["type"] = $this->getType($data['type']);
        $developer = new Developer();
        $form = $this->createForm('AppBundle\Form\DeveloperType', $developer);
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
        $em->persist($developer);
        $em->flush();
        $developer = $this->get('jms_serializer')->serialize($developer, 'json', SerializationContext::create()->setGroups(array('developer_index')));
        return new Response($developer);
    }

    /**
     * @Rest\Get("/{id}", name="developer_show")
     * @param $id
     * @return Response
     */
    public function showAction(Developer $developer)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $developer = $em->getRepository('AppBundle:Developer')->find($id);
        if (!$developer) {
            throw new HttpException(400, "Developer dont exist");
        }
        $developer = $this->get('jms_serializer')->serialize($developer, 'json', SerializationContext::create()->setGroups(array('developer_index')));
        return new Response($developer);
    }

    /**
     * @Rest\Put("/edit/{id}", name="developer_put")
     * @param Request $request
     * @return Response
     * @param $id
     */
    public function editAction(Request $request, Developer $developer)
    {
        $data = json_decode($request->getContent(),true);
        $this->validateAction($data);
        $data["type"] = $this->getType($data['type']);
        $editForm = $this->createForm('AppBundle\Form\DeveloperType', $developer);
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
        $developer = $this->get('jms_serializer')->serialize($developer, 'json', SerializationContext::create()->setGroups(array('developer_index')));
        return new Response($developer);
    }

    /**
     * @Rest\Delete("/remove/{id}", name="developer_delete")
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
        $developer = $em->getRepository('AppBundle:Developer')->find($id);
        if (!$developer) {
            throw new HttpException(400, "Developer dont exist");
        }
        $em->remove($developer);
        $em->flush();
        $message = [
            'type' => 'Delete',
            'description' => 'Developer deleted',
            'code'=> 200,
        ];
        return new JsonResponse($message,200);
    }


    private function getType($type){
        $typesLanguage = array(
            1 => "PHP",
            2 => "NET",
            3 => "Python",
        );
        return $typesLanguage[$type];
    }

    private function validateAction($dataRequest){

        $enterpriseId = isset($dataRequest['enterprise']) ? $dataRequest['enterprise'] : false;
        $typeLanguageId = isset($dataRequest['type']) ? $dataRequest['type'] : false;
        if (!$enterpriseId) {
            throw new HttpException(400, "Enterprise is Required");
        }
        if (!$typeLanguageId) {
            throw new HttpException(400, "Type Language is Required");
        }

        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('AppBundle:Enterprise')->find($enterpriseId);
        $typesLanguage = array(
            1 => "PHP",
            2 => "NET",
            3 => "Python",
        );

        if (!$enterprise) {
            throw new HttpException(400, "Enterprise dont exist");
        }

        if (!isset($typesLanguage[$typeLanguageId])) {
            throw new HttpException(400, "Type Language dont exist, values validate this 1 => PHP, 2 => NET, 3 => Python");
        }
    }
}
