<?php

namespace Pastel\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pastel\PlatformBundle\Entity\Article;
use Pastel\PlatformBundle\Entity\User;
use Pastel\PlatformBundle\Form\ArticleType;

class PlatformController extends Controller
{	
	/**
     * @Route("/", name="pastel_platform_homepage")
     */
	public function indexAction()
	{
		return $this->render('PastelPlatformBundle:Default:index.html.twig');
	}

	/**
     * @Route("/creation", name="pastel_platform_creation")
     */
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
    
	/**
     * @Route("/edition/{id}", name="pastel_platform_edition")
     */
	public function editionAction(Request $request,article $article, $id)
	{
		
		$form   = $this->get('form.factory')->create(ArticleType::class, $article);

		if ($request->isMethod('POST')) {
			
			$form->handleRequest($request);

			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->flush();
			}
		}

		return $this->render('PastelPlatformBundle:Default:edition.html.twig', array(
			'form' => $form->createView(),
			));
	}    
}
