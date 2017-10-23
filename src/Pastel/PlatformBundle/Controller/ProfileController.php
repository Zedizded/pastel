<?php

namespace Pastel\PlatformBundle\Controller;


use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class ProfileController extends BaseController
{
    
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $em = $this->getDoctrine()->getManager();

        // Get back and returns all data associated with an user
        $articles = $em->getRepository('PastelPlatformBundle:Article')->findAll();
        $comments = $em->getRepository('PastelPlatformBundle:Comment')->findBy(array('warning' => '1'));
        $UsersToValid = $em->getRepository('PastelPlatformBundle:User')->findBy(array('pastelMember' => 1));
        $users = $em->getRepository('PastelPlatformBundle:User')->findAll();
        

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'articles' => $articles,
            'comments' => $comments,
            'UsersToValid' => $UsersToValid,
            'users' => $users
        ));
    }

}