<?php

namespace Sevdev\ArkonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sevdev\ArkonTestBundle\Entity\School;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Contains all school-related functions
 */

class SchoolsController extends Controller
{
    /**
     * Lists all schools with admin controls, if user is admin.
     * Home page.
     *
     * @Route("/")
     * @Template("SevdevArkonTestBundle:Schools:index.html.twig")
     */
    public function indexAction()
    {
        // Get all schools
        $em = $this->getDoctrine()->getEntityManager();
        $schools = $em->createQuery(
            'select school from SevdevArkonTestBundle:School school
            where school.deleted = false
            order by school.createdAt desc'
        )->getResult();


        return array(
            'schools' => $schools, 
            'admin' => $this->get('security.context')
                ->isGranted('ROLE_ADMIN')
        );        
    }

    /**
     * Delete a school, only with ROLE_ADMIN
     * @param int id
     *
     * @Route("/delete/{id}")
     * @Template("SevdevArkonTestBundle:Schools:action.html.twig")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $school = $em
            ->getRepository('SevdevArkonTestBundle:School')
            ->findById((int) $id);

        if ($school)
        {
            $school = $school[0];
            $school->delete($em); // logical deletion
            $em->persist($school);
            $em->flush();
        }
        else throw $this->createNotFoundException();

        return array(
            'school' => $school,
            'action' => 'delete',
            'success' => true,
            'display_form' => false,
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
        );
    }

    /**
     * Update school information, only with ROLE_ADMIN
     * @param int id
     *
     * @Route("/edit/{id}")
     * @Template("SevdevArkonTestBundle:Schools:action.html.twig")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction($id)
    {
        // Be optimistic :)
        $error = null;
        $success = true;

        $em = $this->getDoctrine()->getEntityManager();

        if ($id !== 'new')
        {
            // find $id in database
            $school = $em
                ->getRepository('SevdevArkonTestBundle:School')
                ->findById((int) $id);

            if (!$school) throw $this->createNotFoundException();
            $school = $school[0];
        }
        else
        {
            // create new School
            $school = new School();
        }

        if ($this->getRequest()->get('name') && 
            $this->getRequest()->get('type') !== null)
        {
            // process form data
            $display_form = false;
            $success = true;

            try
            {
                $school->setName($this->getRequest()->get('name'));
                $school->setType($this->getRequest()->get('type'));
                $file = $this->getRequest()->files->get('logo');
                if ($file)
                {
                    $school->setLogo($file);
                }

                $em->persist($school);
                $em->flush();
            }
            catch (Exception $e)
            {
                $error = $e->getMessage();
                $success = false;
            }
        }
        else
        {
            // display form
            $display_form = true;
            $success = true;
        }

        return array(
            'school' => $school,
            'action' => 'update',
            'success' => $success,
            'error' => $error,
            'display_form' => $display_form,
            'admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
        );
    } 
}
