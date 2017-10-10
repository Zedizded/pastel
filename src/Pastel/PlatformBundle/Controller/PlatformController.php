<?php

namespace Pastel\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pastel\PlatformBundle\Entity\Article;
use Pastel\PlatformBundle\Entity\User;
use Pastel\PlatformBundle\Form\ArticleType;

class PlatformController extends Controller
{
	public function indexAction()
	{
		return $this->render('PastelPlatformBundle:Default:index.html.twig');
	}

	public function creationAction(Request $request)
	{
		$article = new Article();
		$form   = $this->get('form.factory')->create(ArticleType::class, $article);

		if ($request->isMethod('POST')) {
			
			$form->handleRequest($request);

			if ($form->isValid()) {

				$article->setUser( $this->getUser());

				$em = $this->getDoctrine()->getManager();
				$em->persist($article);
				$em->flush();
			}
		}

		return $this->render('PastelPlatformBundle:Default:creation.html.twig', array(
			'form' => $form->createView(),
			));
	}
}
