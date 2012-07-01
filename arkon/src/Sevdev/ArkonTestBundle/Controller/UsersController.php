<?php

namespace Sevdev\ArkonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sevdev\ArkonTestBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Contains all user management functions
 */
class UsersController extends Controller
{
  /**
   * Returns a User form from FormBuilder
   *
   * @param Sevdev\ArkonTestBundle\Entity\User $user
   * @return Symfony\Component\Form\Form the form
   */
  private function buildUserForm($user)
  {
    return $this->createFormBuilder($user)
      ->add('username', 'text')
      ->add('password', 'password', array('required' => false))
      ->add('role', 'choice', array
        (
          'choices' => array
          (
            '0' => 'ROLE_USER',
            '1' => 'ROLE_ADMIN', 
          )
        )
      )
      ->add('id', 'hidden')->getForm();
  }

  /**
   * Lists all users, and processes posts.
   *
   * @Secure(roles="ROLE_ADMIN")
   * @Route("/")
   * @Template("SevdevArkonTestBundle:Users:index.html.twig")
   */
  public function indexAction()
  {
    // Get all users
    $em = $this->getDoctrine()->getEntityManager();
    $users = $em->getRepository('SevdevArkonTestBundle:User')->findAll();

    // Add an empty user for user creation form
    $users[] = $new_user = new User();

    $new_user->setUsername('Create user');

    $forms = array();

    // Generate form for each user
    foreach ($users as $user)
    {
      $forms[] = $this->buildUserForm($user)->createView();
    }

    return array(
      'users' => $users,
      'forms' => $forms,
      'admin' => true,
    );
  }

  /**
   * Handles POST User modification/deletion
   *
   * @Secure(roles="ROLE_ADMIN")
   * @Route("/edit")
   * @Template("SevdevArkonTestBundle:Users:action.html.twig")
   */
  function postAction()
  {
    $em = $this->getDoctrine()->getEntityManager();
    $id = $this->getRequest()->get('form'); $id = $id['id'];

    $delete = $this->getRequest()->get('delete');
    $success = true; // optimism!

    if ($id)
    {
      $user = $em->getRepository('SevdevArkonTestBundle:User')
        ->findById($id);
      if (!$user) throw $this->createNotFoundException();
      $user = $user[0];
    }
    else // create new user
      $user = new User();

    if ($delete)
    {
      $em->remove($user);
    }
    else
    {
      $form = $this->buildUserForm($user);
      
      // remove id
      $form->remove('id');

      // check if we received a new password
      $password_set = $this->getRequest()->get('form'); 
      $password_set = (bool) trim($password_set['password']);

      // remove password, if empty
      if (!$password_set) $form->remove('password');

      $form->bindRequest($this->getRequest());

      $em->persist($user);
    }

    $em->flush();

    return array(
      'user' => $user,
      'success' => $success,
      'action' => $delete ? 'delete' : 'update',
      'admin' => true,
    );
  }

  /**
   * Display login form and errors. Everything else is done by Symfony.
   *
   * @Route("/login")
   * @Template("SevdevArkonTestBundle:Users:login.html.twig")
   */
  function loginAction()
  {
    $request = $this->getRequest();
    $session = $request->getSession();
 
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) 
    {
      $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    } 
    else 
    {
      $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
      $session->remove(SecurityContext::AUTHENTICATION_ERROR);
    }

    return array('error' => $error);
  }
}

