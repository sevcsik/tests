<?php

namespace Sevdev\ArkonTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sevdev\ArkonTestBundle\Entity\Study
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Study
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date $start
     *
     * @ORM\Column(name="start", type="date")
     */
    private $start;

    /**
     * @var date $finish
     *
     * @ORM\Column(name="finish", type="date")
     */
    private $finish;

    /**
     * @var Student $student
     *
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="studies")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

    /**
     * @var School $school
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="studies")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id")
     */
    private $school;

    /**
     * @var smallint $type education type (same as school types)
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

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
     * Set start
     *
     * @param date $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Get start
     *
     * @param string format date format
     * @return date 
     */
    public function getStart($format = null)
    {
        if ($format) return $this->start->format($format);
        else return $this->start;
    }

    /**
     * Set finish
     *
     * @param date $finish
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;
    }

    /**
     * Get finish
     *
     * @param string format date format
     * @return date 
     */
    public function getFinish($format = null)
    {
        if ($format) return $this->finish->format($format);
        else return $this->finish;
    }

    /**
     * Set type
     *
     * @param smallint $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @param bool string return value in a human-readable form
     *
     * @return smallint 
     */
    public function getType($string = false)
    {
        $types = array(
            'elementary school', 
            'high school', 
            'university or college',
        );

        if ($string)
            return $types[$this->type];    
        else return $this->type;
    }

    /**
     * Set student
     *
     * @param Sevdev\ArkonTestBundle\Entity\Student $student
     */
    public function setStudent(\Sevdev\ArkonTestBundle\Entity\Student $student)
    {
        $this->student = $student;
    }

    /**
     * Get student
     *
     * @return Sevdev\ArkonTestBundle\Entity\Student 
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set school
     *
     * @param Sevdev\ArkonTestBundle\Entity\School $school
     */
    public function setSchool(\Sevdev\ArkonTestBundle\Entity\School $school)
    {
        $this->school = $school;
    }

    /**
     * Get school
     *
     * @return Sevdev\ArkonTestBundle\Entity\School 
     */
    public function getSchool()
    {
        return $this->school;
    }
}
