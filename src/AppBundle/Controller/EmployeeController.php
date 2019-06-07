<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Traits\FormErrorValidator;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Employee controller.
 *
 * @Route("employee")
 */
class EmployeeController extends Controller
{
    use FormErrorValidator;

   /**
     * @Rest\Get("/", name="employee_index")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository('AppBundle:Employee')->findAll();
        $employees = $this->get('jms_serializer')->serialize($employees, 'json', SerializationContext::create()->setGroups(array('developer_index','designer_index')));
        return new Response($employees);
    }

    /**
     * @Rest\Get("/{id}", name="employee_show")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository('AppBundle:Employee')->find($id);
        if (!$employee) {
            throw new HttpException(400, "Employee dont exist");
        }
        $employee = $this->get('jms_serializer')->serialize($employee, 'json', SerializationContext::create()->setGroups(array('developer_index','designer_index')));
        return new Response($employee);
    }

    /**
     * @Rest\Delete("/remove/{id}", name="employee_delete")
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
        $employee = $em->getRepository('AppBundle:Employee')->find($id);
        if (!$employee) {
            throw new HttpException(400, "Employee dont exist");
        }
        $em->remove($employee);
        $em->flush();
        $message = [
            'type' => 'Delete',
            'description' => 'Employee deleted',
            'code'=> 200,
        ];
        return new JsonResponse($message,200);
    }

    /**
     * @Rest\Get("/average", name="employee_average")
    */
    public function averageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository('AppBundle:Employee')->findAll();
        foreach ($employees as $employee) {
            $birthDate = $employee->getBirthDate();
            //explode the date to get month, day and year
            $birthDate = explode("/", $birthDate);
            //get age from date or birthdate
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
              ? ((date("Y") - $birthDate[2]) - 1)
              : (date("Y") - $birthDate[2]));
            echo "Age is:" . $age;
        }
        die();
        $employees = $this->get('jms_serializer')->serialize($employees, 'json', SerializationContext::create()->setGroups(array('developer_index','designer_index')));
        return new Response($employees);
    }
}
