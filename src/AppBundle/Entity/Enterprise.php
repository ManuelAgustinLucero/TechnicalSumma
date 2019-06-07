<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Enterprise
 *
 * @ORM\Table(name="enterprise")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EnterpriseRepository")
 */
class Enterprise
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"developer_index", "designer_index","enterprise_index"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Groups({"developer_index", "designer_index","enterprise_index"})
     * @Assert\NotBlank(message="Name is required")
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @JMS\Groups({"enterprise_index"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="enterprise")
     * @JMS\Groups({"enterprise_index"})
     */
    private $employee;

    public function __construct()
    {
        $this->employee = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Enterprise
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Enterprise
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone("America/Argentina/Cordoba"));

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return Enterprise
     */
    public function addEmployee(\AppBundle\Entity\Employee $employee)
    {
        $this->employee[] = $employee;

        return $this;
    }

    /**
     * Remove employee
     *
     * @param \AppBundle\Entity\Employee $employee
     */
    public function removeEmployee(\AppBundle\Entity\Employee $employee)
    {
        $this->employee->removeElement($employee);
    }

    /**
     * Get employee
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployee()
    {
        return $this->employee;
    }
}
