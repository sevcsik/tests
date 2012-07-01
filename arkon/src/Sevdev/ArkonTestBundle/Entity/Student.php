<?php

namespace Sevdev\ArkonTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sevdev\ArkonTestBundle\Entity\Student
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sevdev\ArkonTestBundle\Entity\StudentRepository")
 */
class Student
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean $gender
     *
     * @ORM\Column(name="gender", type="boolean")
     */
    private $gender;

    /**
     * @var date $birthDate
     *
     * @ORM\Column(name="birthDate", type="date")
     */
    private $birthDate;

    /**
     * @var array $studies
     *
     * @ORM\OneToMany(targetEntity="Study", mappedBy="student", cascade={"remove"})
     */
    private $studies;

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
     * Set name (sanitized)
     *
     * @param string $name
     * @throws Exception if name exceeds 255 characters
     */
    public function setName($name)
    {
        if (strlen($name) > 255) 
            throw new Exception('Name length must not exceed 255 characters.');
        $this->name = filter_var($name, FILTER_SANITIZE_STRING);
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
     * Set gender
     *
     * @param boolean $gender false for male, true for female
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @param bool string return in human-readable string
     * @return mixed gender string or boolean (false for male)
     */
    public function getGender($string = false)
    {
        if ($string) 
            return $this->gender ? 'female' : 'male';
        else
            return $this->gender;
    }

    /**
     * Set birthDate
     *
     * @param date $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birthDate
     *
     * @param string format date format
     * @return mixed date if !format, otherwise string
     */
    public function getBirthDate($format = null)
    {
        if ($format) return $this->birthDate->format($format);
        else return $this->birthDate;
    }

    public function __construct()
    {
        $this->studies = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add studies
     *
     * @param Sevdev\ArkonTestBundle\Entity\Study $studies
     */
    public function addStudy(\Sevdev\ArkonTestBundle\Entity\Study $studies)
    {
        $this->studies[] = $studies;
    }

    /**
     * Get studies
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getStudies()
    {
        return $this->studies;
    }

    /**
     * Get number of studies
     *
     * @return int
     */
    public function getNumberOfStudies()
    {
        return sizeof($this->studies);
    }

    /**
     * Get highest education level
     * 
     * @param bool string respond in human-readable form
     * @return mixed int or string
     */
    public function getHighestEducation($string = false)
    {
        $max = -1;
        foreach ($this->studies as $study)
        {
            if ($study->getType() > $max) $max = $study->getType();
        }

        $types = array(
            'elementary school', 
            'high school', 
            'university or college',
        );

        if ($string)
            return $max == -1 ? 'n/a' : $types[$max];
        else return $max;
    }
}
