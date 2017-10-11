<?php

namespace Pastel\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pastel\PlatformBundle\Entity\User;
use Pastel\PlatformBundle\Entity\Article;
use Pastel\PlatformBundle\Entity\ArticlePicture;
use Pastel\PlatformBundle\Entity\Comment;
use Pastel\PlatformBundle\Form\ArticleType;
use Pastel\PlatformBundle\Form\CommentType;

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
     * @Route("/blog", name="pastel_platform_blog")
     */
	public function blogAction()
	{
		$em = $this->getDoctrine()->getManager();

		$articles = $em->getRepository('PastelPlatformBundle:Article')->findAll();

		return $this->render('PastelPlatformBundle:Default:blog.html.twig', array(
			'articles' => $articles
        ));
	}    

	/**
     * @Route("/blog/creation", name="pastel_platform_creation")
     */
    public function creationAction(Request $request)
	{
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $article = new Article();
			$form = $this->get('form.factory')->create(ArticleType::class, $article);
            
            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				$em = $this->getDoctrine()->getManager();
                
				$article->setUser($this->getUser());
				$em->persist($article);
                
				$articlePicture = $article->getArticlePicture();
				if ($articlePicture !== null)
				{
					$articlePicture->setAlt($article->getTitle());
					$articlePicture->setRandom(rand());
					$em->persist($articlePicture);
				}       
                
				$em->flush();
                
                $request->getSession()->getFlashBag()->add('info', 'Votre article a bien été enregistrée.');
                
                $article = new Article();
                $form = $this->get('form.factory')->create(ArticleType::class, $article);
			}
            
            return $this->render('PastelPlatformBundle:Default:creation.html.twig', array(
				'form' => $form->createView()
            ));
		}

		return $this->redirectToRoute('pastel_platform_homepage');
	}
    
	/**
     * @Route("/blog/edition/{id}", name="pastel_platform_edition")
     */
	public function editionAction(Request $request, Article $article, $id)
	{
		
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$form = $this->get('form.factory')->create(ArticleType::class, $article);

			if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				$em = $this->getDoctrine()->getManager();
                $articlePicture = $article->getArticlePicture();

				if ($articlePicture !== null)
				{
					$articlePicture->setRandom(rand());
					$articlePicture->setAlt($article->getTitle());
					$em->persist($articlePicture);
				}
                
				$em->flush();
                
                $request->getSession()->getFlashBag()->add('info', 'Votre article a bien été enregistrée.');
			}

            return $this->render('PastelPlatformBundle:Default:edition.html.twig', array(
				'form' => $form->createView(),
                'article' => $article
            ));
		}
		return $this->redirectToRoute('pastel_platform_homepage');
	}
    
	/**
     * @Route("/blog/article/{id}", name="pastel_platform_article")
     */
	public function articleAction(Request $request, Article $article, $id)
	{

		$comment = new Comment();
		$form = $this->get('form.factory')->create(CommentType::class, $comment);

		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			$comment->setUser($this->getUser());
			$comment->setArticle($article);
            
			$em->persist($comment);
			$em->flush();
			
			// Clears the form
			$comment = new Comment();
			$form = $this->get('form.factory')->create(CommentType::class, $comment);
		}

		$comments = $em->getRepository('PastelPlatformBundle:Comment')->findBY(array('article' => $article),array('id' => 'desc'));        

		return $this->render('PastelPlatformBundle:Default:article.html.twig', array(
            'form' => $form->createView(),
			'article' => $article,
			'comments' => $comments
        ));
	}
    
	/**
     * @Route("/blog/comment/{id}", name="pastel_platform_signalComment")
     */
	public function signalCommentAction(Request $request, Comment $comment, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$comment->setWarning(true);
		$em->flush();

		$request->getSession()->getFlashBag()->add('info', 'Le commentaire a bien été signalé.');

		return $this->redirectToRoute('pastel_platform_article', array('id' => $comment->getArticle()->getId()));

	}    
    
/**
     * @Route("/blog/suppression/{id}", name="pastel_platform_suppression")
     */
	public function suppressionAction(Request $request, Article $article, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			$em->remove($article);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', "L'article a bien été supprimer");

			return $this->redirectToRoute('fos_user_profile_show');
		}
		return $this->redirectToRoute('pastel_platform_homepage');

	}

	/**
     * @Route("/blog/commentValidation/{id}", name="pastel_platform_commentValidation")
     */
	public function commentValidationAction(Request $request, Comment $comment, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			$comment->setWarning(false);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', "Le commentaire a bien été validé");

			return $this->redirectToRoute('fos_user_profile_show');
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}

	/**
     * @Route("/blog/commentSuppression/{id}", name="pastel_platform_commentSuppression")
     */
	public function commentSuppressionAction(Request $request, Comment $comment, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			$em->remove($comment);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', "Le commentaire a bien été supprimé");

			return $this->redirectToRoute('fos_user_profile_show');
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}    
    
	/**
     * @Route("/blog/pictureSuppression/{id}/{articleId}", name="pastel_platform_pictureSuppression")
     */
	public function pictureSuppressionAction(Request $request, ArticlePicture $articlePicture, $id, $articleId)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			$em->remove($articlePicture);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', "L'image à été supprimé");

			return $this->redirectToRoute('pastel_platform_edition',  array('id' => $articleId));
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}    
}
