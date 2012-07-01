<?php

namespace Sevdev\ArkonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sevdev\ArkonTestBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Contains all student-related functions
 */
class StudentsController extends Controller
{

    /**
     * List all students with admin controls, if user is admin.
     *
     * It's possible to filter with GET parameters:
     *
     * - by_school={school_id}
     * - by_gender={0|1}
     * - by_name={substring}
     * - by_study_start_date={YYYY-MM-DD}
     * - by_study_end_date={YYYY-MM-DD}
     * - by_birth_date={YYYY-MM-DD}
     * All filters but the first in the query string are ignored.
     *
     * @Route("/")
     * @Template("SevdevArkonTestBundle:Students:index.html.twig")
     */
    public function indexAction()
    {
        // Get filters
        $req = $this->getRequest();
        $by_school = (int) $req->get('by_school');
        $by_gender = $req->get('by_gender') !== null ?
          (int) $req->get('by_gender') : null;
        $by_name = $req->get('by_name');
        $by_birth_date = $req->get('by_birth_date') ?
          new \DateTime($req->get('by_birth_date')) : null;
        $by_study_start_date = $req->get('by_study_start_date') ?
          new \DateTime($req->get('by_study_start_date')) : null;
        $by_study_end_date = $req->get('by_study_end_date') ? 
          new \DateTime($req->get('by_study_end_date')) : null;

        // Build Query
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('student')->from('SevdevArkonTestBundle:Student', 'student')
          ->orderBy('student.name')->groupBy('student.id');

        if ($by_name) 
        {
          $qb->where("student.name like '%$by_name%'");
        }
        else if ($by_gender !== null) 
        {
          $qb->where('student.gender = :by_gender');
          $qb->setParameter('by_gender', $by_gender);
        }
        else if ($by_birth_date)
        {
          $qb->where('student.birthDate = :by_birth_date');
          $qb->setParameter('by_birth_date', $by_birth_date);
        }

        // Apply study-related filters
        else if ($by_school || $by_study_start_date || $by_study_end_date)
        {
          $qb->join('student.studies', 'study');

          if ($by_school) 
          {
            $qb->where('study.school = :by_school');
            $qb->setParameter('by_school', $by_school);
          }
          else if ($by_study_start_date) 
          {
            $qb->where('study.start = :by_study_start_date');
            $qb->setParameter('by_study_start_date', $by_study_start_date);
          }
          else if ($by_study_end_date)
          {
            $qb->where('study.finish = :by_study_end_date');
            $qb->setParameter('by_study_end_date', $by_study_end_date);
          }
        }

        $students = $qb->getQuery()->getResult();

        // Fetch schools for filters
        $schools = $em->getRepository('SevdevArkonTestBundle:School')
          ->findByDeleted(false);

        return array(
            'students' => $students,
            'schools' => $schools,
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
        );
    }

    /**
     * Displays edit form and processes form data
     *
     * @param int $id
     * @Route("/edit/{id}")
     * @Template("SevdevArkonTestBundle:Students:action.html.twig")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $success = true;
        $display_form = true;
        $em = $this->getDoctrine()->getEntityManager();

        if ($id != 'new')
        {
            $id = (int) $id;
            $student = $em->getRepository('SevdevArkonTestBundle:Student')
                ->findById($id);

            if (!$student) throw $this->createNotFoundException();

            $student = $student[0];
        }
        else
        {
            $student = new Student();
        }

        // create form
        $form = $this->createFormBuilder($student)
            ->add('name', 'text', array('required' => 'true'))
            ->add('birthDate', 'birthday')
            ->add('gender', 'choice', array(
                'choices' => array('0' => 'male', '1' => 'female')
            ))
            ->getForm();

        if ($this->getRequest()->getMethod() == 'POST')
        {
            $display_form = false;
            $form->bindRequest($this->getRequest());

            if ($form->isValid())
            {
                $em->persist($student);
                $em->flush();

                $success = true;
            }
            else $success = false;
        }

        return array(
            'action' => 'update',
            'student' => $student,
            'form' => $form->createView(),
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
            'success' => $success,
            'display_form' => $display_form                
        );
    }

    /**
     * Deletes a student FOREVER.
     *
     * @param int $id
     * @Route("/delete/{id}")
     * @Template("SevdevArkonTestBundle:Students:action.html.twig")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $success = true;
        $display_form = false;
        $em = $this->getDoctrine()->getEntityManager();

        $id = (int) $id;
        $student = $em->getRepository('SevdevArkonTestBundle:Student')
            ->findById($id);

        if (!$student) throw $this->createNotFoundException();

        $student = $student[0];

        // Delete studies
        $em->createQuery('
            delete from SevdevArkonTestBundle:Study s 
            where s.student = '.$student->getId()
        )->execute();

        // Delete student
        $em->remove($student);
        $em->flush();

        return array(
            'action' => 'delete',
            'student' => $student,
            'form' => null,
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
            'success' => $success,
            'display_form' => false
        );
    }
}

