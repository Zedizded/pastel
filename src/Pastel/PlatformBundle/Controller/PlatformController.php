<?php

namespace Pastel\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pastel\PlatformBundle\Form\ContactType;
use Pastel\PlatformBundle\Entity\User;
use Pastel\PlatformBundle\Entity\Article;
use Pastel\PlatformBundle\Entity\ArticlePicture;
use Pastel\PlatformBundle\Entity\Comment;
use Pastel\PlatformBundle\Form\ArticleType;
use Pastel\PlatformBundle\Form\CommentType;
use Pastel\PlatformBundle\Entity\Search;
use Pastel\PlatformBundle\Form\SearchType;
use Pastel\PlatformBundle\Entity\AddFile;
use Pastel\PlatformBundle\Form\AddFileType;
use Swift_Message;

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
     * @Route("/actualites", name="pastel_platform_blog")
     */
	public function blogAction(Request $request)
	{
        // Creates the search type for the blog
        $search = new Search();
		$form   = $this->get('form.factory')->create(SearchType::class, $search);
        
		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			// If a search has been submitted, shows items matching the search
            $content = $search->getContent();

			$articles = $em->getRepository('PastelPlatformBundle:Article')->complexFind($content);

			$search = new Search();
			$form   = $this->get('form.factory')->create(SearchType::class, $search);

		} 
		else {
            // Shows all articles
			$articles = $em->getRepository('PastelPlatformBundle:Article')->classicFind();
		}

		return $this->render('PastelPlatformBundle:Default:blog.html.twig', array(
			'articles' => $articles,
            'form' => $form->createView()
        ));
	}    

	/**
     * @Route("/actualites/creation", name="pastel_platform_creation")
     */
    public function creationAction(Request $request)
	{
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            // Creation of a new blog's article
            $article = new Article();
			$form = $this->get('form.factory')->create(ArticleType::class, $article);
            
            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				// Records the new article in the database
                $em = $this->getDoctrine()->getManager();
				$article->setUser($this->getUser());
				$em->persist($article);
                
				$articlePicture = $article->getArticlePicture();
				if ($articlePicture !== null)
				{
					$articlePicture->setAlt($article->getTitle());
                    $articlePicture->setArticle($article);
					$articlePicture->setRandom(rand());
					$em->persist($articlePicture);
				}       
                
				$em->flush();
                
                $request->getSession()->getFlashBag()->add('info', 'Votre article a bien été enregistré.');
                
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
     * @Route("/actualites/edition/{id}", name="pastel_platform_edition")
     */
	public function editionAction(Request $request, Article $article, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			// Modification of the article of id "$id" (already present in the database)
            $form = $this->get('form.factory')->create(ArticleType::class, $article);

			if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				$em = $this->getDoctrine()->getManager();
                $articlePicture = $article->getArticlePicture();

				if ($articlePicture !== null)
				{
					$articlePicture->setRandom(rand());
					$articlePicture->setAlt($article->getTitle());
                    $articlePicture->setArticle($article);
					$em->persist($articlePicture);
				}
                
				// Saves the modified article in the database
                $em->flush();
                
                $request->getSession()->getFlashBag()->add('info', 'Votre article a bien été enregistré.');
			}

            return $this->render('PastelPlatformBundle:Default:edition.html.twig', array(
				'form' => $form->createView(),
                'article' => $article
            ));
		}
		return $this->redirectToRoute('pastel_platform_homepage');
	}
    
	/**
     * @Route("/actualites/article/{id}", name="pastel_platform_article")
     */
	public function articleAction(Request $request, Article $article, $id)
	{
		// Creates a new comment
        $comment = new Comment();
		$form = $this->get('form.factory')->create(CommentType::class, $comment);

		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			// If a new comment is entered, saves it in the database
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
     * @Route("/actualites/comment/{id}", name="pastel_platform_signalComment")
     */
	public function signalCommentAction(Request $request, Comment $comment, $id)
	{
		$em = $this->getDoctrine()->getManager();

		// Registers a warning in the database when a comment is reported
        $comment->setWarning(true);
		$em->flush();

		$request->getSession()->getFlashBag()->add('info', 'Le commentaire a bien été signalé.');

		return $this->redirectToRoute('pastel_platform_article', array('id' => $comment->getArticle()->getId()));

	}    
    
    /**
     * @Route("/actualites/suppression/{id}", name="pastel_platform_suppression")
     */
	public function suppressionAction(Request $request, Article $article, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			// Removes an article already present in the database from it
            $em->remove($article);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', 'L\'article a bien été supprimé');

			return $this->redirectToRoute('fos_user_profile_show');
		}
		return $this->redirectToRoute('pastel_platform_homepage');

	}

	/**
     * @Route("/actualites/commentValidation/{id}", name="pastel_platform_commentValidation")
     */
	public function commentValidationAction(Request $request, Comment $comment, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			// If a warning has been register on a comment but is not justified, deletes this warning
            $comment->setWarning(false);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', 'Le commentaire a bien été validé');

			return $this->redirectToRoute('fos_user_profile_show');
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}

	/**
     * @Route("/actualites/commentSuppression/{id}", name="pastel_platform_commentSuppression")
     */
	public function commentSuppressionAction(Request $request, Comment $comment, $id)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			// If a warning has been register on a comment and is justified, deletes the comment from the database
            $em->remove($comment);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', 'Le commentaire a bien été supprimé');

			return $this->redirectToRoute('fos_user_profile_show');
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}    
    
	/**
     * @Route("/actualites/pictureSuppression/{id}/{articleId}", name="pastel_platform_pictureSuppression")
     */
	public function pictureSuppressionAction(Request $request, ArticlePicture $articlePicture, $id, $articleId)
	{
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
			
			$em = $this->getDoctrine()->getManager();

			// Removes from the database a picture associated with an article
            $em->remove($articlePicture);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', 'L\'image a été supprimée');

			return $this->redirectToRoute('pastel_platform_edition',  array('id' => $articleId));
		}

		return $this->redirectToRoute('pastel_platform_homepage');

	}  
    
    /**
     * @Route("/contact", name="pastel_platform_contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->get('form.factory')->create(ContactType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $post = $request->request->get('pastel_platformbundle_contact_form');
            $message = new Swift_Message();
            // Defines the parameters of the message
            $message->setSubject('Message du site "Castres Pastel"')
            ->setFrom(array('lions.castrespastel@gmail.com' => 'Lions Club Castres Pastel'))
                ->setTo('lions.castrespastel@gmail.com')
                ->setContentType('text/html')
                ->setCharset('utf-8')
                ->setBody(
                    $this->renderView('PastelPlatformBundle:Default:email.html.twig',
                        array('post' => $post)));
            // Sends the message
            $this->get('mailer')->send($message);

            $form = $this->get('form.factory')->create(ContactType::class);

            $request->getSession()->getFlashBag()->add('info', 'Votre message a bien été envoyé.');
            return $this->render('PastelPlatformBundle:Default:contact.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $this->render('PastelPlatformBundle:Default:contact.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    
    /**
     * @Route("/profil/userValidation/{id}", name="pastel_platform_userValidation")
     */
    public function userValidationAction(Request $request, User $user, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            
            $em = $this->getDoctrine()->getManager();

            // Promotes an user as Pastel Member
            $user->setPastelMember(0);
            $user->addRole('ROLE_PASTEL');
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Validation réussie');

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->redirectToRoute('pastel_platform_homepage');

    }

    /**
     * @Route("/profil/userReset/{id}", name="pastel_platform_userReset")
     */
    public function userResetAction(Request $request, User $user, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            
            $em = $this->getDoctrine()->getManager();

            // Refuses the status of naturalist and deletes hold
            $user->setPastelMember(0);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Mise à jour de l\'utilisateur réussie');

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->redirectToRoute('pastel_platform_homepage');

    }
    
    /**
     * @Route("/profil/userDelete/{id}", name="pastel_platform_userDelete")
     */
    public function userDeleteAction(Request $request, User $user, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $em = $this->getDoctrine()->getManager();
            $userToRemove = $em->getRepository('PastelPlatformBundle:User')->find($id);

            // Removes user with id "$id" from the database
            $em->remove($userToRemove);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Suppression de l'utilisateur réussie");

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->redirectToRoute('pastel_platform_homepage');

    }
    
	/**
     * @Route("/profil/addFile", name="pastel_platform_addfile")
     */
    public function addFileAction(Request $request)
	{
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            // Creation of a new file to add
            $addFile = new AddFile();
			$form = $this->get('form.factory')->create(AddFileType::class, $addFile);
            
            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

				// Records the new file in the database
                $em = $this->getDoctrine()->getManager();
				$em->persist($addFile);
                
				$em->flush();
                
                $request->getSession()->getFlashBag()->add('info', 'Votre fichier a bien été enregistré.');
                
                $addFile = new AddFile();
                $form = $this->get('form.factory')->create(AddFileType::class, $addFile);
			}
            
            return $this->render('PastelPlatformBundle:Default:addfile.html.twig', array(
				'form' => $form->createView()
            ));
		}

		return $this->redirectToRoute('fos_user_profile_show');
	}
    
    /**
     * @Route("/profil/fileDelete/{id}", name="pastel_platform_fileDelete")
     */
    public function fileDeleteAction(Request $request, AddFile $addFile, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $em = $this->getDoctrine()->getManager();
            $fileToRemove = $em->getRepository('PastelPlatformBundle:AddFile')->find($id);

            // Removes file with id "$id" from the database
            $em->remove($fileToRemove);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Suppression du fichier réussie");

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->redirectToRoute('pastel_platform_homepage');

    }
    
    /**
     * @Route("/mentions-legales", name="pastel_platform_mentions")
     */
	public function mentionsAction()
	{
		return $this->render('PastelPlatformBundle:Default:mentions.html.twig');
	}
}
