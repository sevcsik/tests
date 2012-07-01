<?php

namespace Sevdev\ArkonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sevdev\ArkonTestBundle\Entity\Study;
use Sevdev\ArkonTestBundle\Entity\School;
use Sevdev\ArkonTestBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Contains all study-related functions
 */
class StudiesController extends Controller
{
    /**
     * Edit a study - display form or process form data.
     *
     *
     * Preset school or student can be selected with studentID and
     * schoolID query string parameters.
     *
     * @param mixed $id can be a study ID, or 'new'.
     * @Route("/edit/{id}")
     * @Template("SevdevArkonTestBundle:Studies:action.html.twig")
     */
    public function editAction($id)
    {
        $success = true;
        $display_form = true;
        $em = $this->getDoctrine()->getEntityManager();

        // look for get parameters
        $student_id = (int) $this->getRequest()->get('studentID');
        $school_id = (int) $this->getRequest()->get('schoolID');

        if ($id != 'new')
        {
            $id = (int) $id;

            $study = $em->getRepository('SevdevArkonTestBundle:Study')
                ->findById($id);

            if (!$study) throw $this->createNotFoundException();

            $study = $study[0];
        }
        else
        {
            $study = new Study();

            if ($student_id)
            {
                $student = $em->getRepository('SevdevArkonTestBundle:Student')
                    ->findByid($student_id);
                if ($student) $study->setStudent($student[0]);
            }
            if ($school_id) 
            {
                $school = $em->getRepository('SevdevArkonTestBundle:School')
                    ->findByid($school_id);
                if ($school) $study->setSchool($school[0]);
            }
        }

        // create form
        $form = $this->createFormBuilder($study)
            ->add('student', 'entity', 
                array('class' => 'SevdevArkonTestBundle:Student',
                'property' => 'name'))
            ->add('school', 'entity', array(
                'class' => 'SevdevArkonTestBundle:School',
                'property' => 'name',
                'query_builder' => $em->createQueryBuilder()
                    ->select('school')
                    ->from('SevdevArkonTestBundle:School', 'school')
                    ->where('school.deleted = 0')
            ))
            ->add('start', 'date')
            ->add('finish', 'date')
            ->add('type', 'choice', 
            array('choices' => array(
                    '0' => 'elementary school', 
                    '1' => 'high school',
                    '2' => 'university/college'
                ))
            )->getForm();

        if ($this->getRequest()->getMethod() == 'POST')
        {
            $display_form = false;
            $form->bindRequest($this->getRequest());

            if ($form->isValid())
            {
                $em->persist($study);
                $em->flush();

                $success = true;
            }
            else $success = false;
        }

        return array(
            'action' => 'update',
            'study' => $study,
            'form' => $form->createView(),
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
            'success' => $success,
            'display_form' => $display_form
        );
    }

    /**
     * List all studies by student or school
     *
     * @param string $list_by 'by_school' or 'by_student'
     * @param int $id school or student id
     * @Route("/{list_by}/{id}")
     * @Template("SevdevArkonTestBundle:Studies:index.html.twig")
     */
    public function indexAction($list_by, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $id = (int) $id;

        if ($list_by == 'by_school')
        {
            $by_school = true;
            $by_student = false;
        }
        else if ($list_by == 'by_student')
        {
            $by_school = false;
            $by_student = true;
        }
        else throw new \Exception('Invalid value for $list_by');

        // find parent
        if ($by_school)
            $parent = $em->getRepository('SevdevArkonTestBundle:School')
                ->findById($id);
        else
            $parent = $em->getRepository('SevdevArkonTestBundle:Student')
                ->findById($id);

        if (!$parent) throw $this->createNotFoundException();

        $parent = $parent[0];

        // fetch children
        $repo = $em->getRepository('SevdevArkonTestBundle:Study');

        $studies = $by_school ? 
            $repo->findBySchool($parent->getId()) :
            $repo->findByStudent($parent->getId());

        return array(
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
            'studies' => $studies,
            'by_school' => $by_school,
            'by_student' => $by_student,
            'parent' => $parent
        );
    }

}
