<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Designer;
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
 * Designer controller.
 *
 * @Route("designer")
 */
class DesignerController extends Controller
{
    use FormErrorValidator;

    /**
     * @Rest\Get("/", name="designer_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $designers = $em->getRepository('AppBundle:Designer')->findAll();
        $designers = $this->get('jms_serializer')->serialize($designers, 'json', SerializationContext::create()->setGroups(array('designer_index')));
        return new Response($designers);
    }

    /**
     * @Rest\Post("/new", name="designer_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);
        $this->validateAction($data);
        $data["type"] = $this->getType($data['type']);
        $designer = new Designer();
        $form = $this->createForm('AppBundle\Form\DesignerType', $designer);
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
        $em->persist($designer);
        $em->flush();
        $designer = $this->get('jms_serializer')->serialize($designer, 'json', SerializationContext::create()->setGroups(array('designer_index')));
        return new Response($designer);
    }

     /**
     * @Rest\Get("/{id}", name="enterprise_show")
     * @param $id
     * @return Response
     */
    public function showAction(Designer $designer)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $designer = $em->getRepository('AppBundle:Designer')->find($id);
        if (!$designer) {
            throw new HttpException(400, "Designer dont exist");
        }
        $designer = $this->get('jms_serializer')->serialize($enterprise, 'json', SerializationContext::create()->setGroups(array('designer_index')));
        return new Response($designer);
    }

     /**
     * @Rest\Put("/edit/{id}", name="designer_put")
     * @param Request $request
     * @return Response
     * @param $id
     */
    public function editAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $designer = $em->getRepository('AppBundle:Designer')->findOneById($id);
        if (!$designer) {
            throw new HttpException(400, "Designer dont exist");
        }
        $data = json_decode($request->getContent(),true);
        $this->validateAction($data);
        $data["type"] = $this->getType($data['type']);
        $editForm = $this->createForm('AppBundle\Form\DesignerType', $designer);
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
        $designer = $this->get('jms_serializer')->serialize($designer, 'json', SerializationContext::create()->setGroups(array('designer_index')));
        return new Response($designer);
    }

    /**
     * @Rest\Delete("/remove/{id}", name="designer_delete")
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
        $designer = $em->getRepository('AppBundle:Designer')->find($id);
        if (!$designer) {
            throw new HttpException(400, "Designer dont exist");
        }
        $em->remove($designer);
        $em->flush();
        $message = [
            'type' => 'Delete',
            'description' => 'Designer deleted',
            'code'=> 200,
        ];
        return new JsonResponse($message,200);
    }

    private function getType($type){
        $typesDesigner = array(
            1 => "Gráfico",
            2 => "Web",
        );
        return $typesDesigner[$type];
    }

    private function validateAction($dataRequest){

        $enterpriseId = isset($dataRequest['enterprise']) ? $dataRequest['enterprise'] : false;
        $typesDesignerId = isset($dataRequest['type']) ? $dataRequest['type'] : false;
        if (!$enterpriseId) {
            throw new HttpException(400, "Enterprise is Required");
        }
        if (!$typesDesignerId) {
            throw new HttpException(400, "Type designer is Required");
        }

        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('AppBundle:Enterprise')->find($enterpriseId);
        $typesDesigner = array(
            1 => "Gráfico",
            2 => "Web",
        );

        if (!$enterprise) {
            throw new HttpException(400, "Enterprise dont exist");
        }

        if (!isset($typesDesigner[$typesDesignerId])) {
            throw new HttpException(400, "Type designer dont exist, values validate this 1 => Gráfico, 2 => Web");
        }
    }
}
