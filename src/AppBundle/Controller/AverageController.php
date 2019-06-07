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
 * @Route("average")
 */
class AverageController extends Controller
{
    /**
     * @Rest\Get("/all", name="average_average_all")
    */
    public function averageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository('AppBundle:Employee')->findAll();
        $array = array();
        foreach ($employees as $employee) {
            $now = time();
            $dob = strtotime($employee->getBirthDate()->format('Y-m-d'));
            $difference = $now - $dob;
            $age = floor($difference / 31556926);
            array_push($array,$age);
        }
        $average = array_sum($array) / count($array);
        $message = [
            'type' => 'Average',
            'description' => 'Average the employes',
            'average' => round($average),
        ];
        return new JsonResponse($message,200);
    }
    /**
     * @Rest\Get("/byEnterprise/{id}", name="average_average_enterprise")
     * @param $id
     * @return Response
     */
    public function averageByEnterpriseAction($id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository('AppBundle:Employee')->findByEnterprise($id);
        if (!$employees) {
            throw new HttpException(400, "Enterprise dont exist");
        }
        $array = array();
        foreach ($employees as $employee) {
            $now = time();
            $dob = strtotime($employee->getBirthDate()->format('Y-m-d'));
            $difference = $now - $dob;
            $age = floor($difference / 31556926);
            array_push($array,$age);
        }
        $average = array_sum($array) / count($array);
        $message = [
            'type' => 'Average',
            'description' => 'Average the Enterprise employes',
            'average' => round($average),
        ];
        return new JsonResponse($message,200);
    }
}
