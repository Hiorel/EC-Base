<?php

namespace App\Controller;

use DateTime;
use App\Entity\Image;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Content;
use App\Form\CommentType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
    * Permet d'afficher un article
    *
    * @Route("/article/{id}/{page<\d+>?1}", name="article_show")
    * 
    * @return void
    */
    public function showArticle(Request $request, EntityManagerInterface $manager, Article $article, $page, PaginationService $pagination) {
        if($article->getType()->getName() == "Lodestone")
        {
            return $this->redirect($article->getUrlExt());
        }else{ 
            $pagination->setEntityClass(Comment::class)
            ->setPage($page)
            ->setTemplatePath('partials/paginationAlt2.html.twig')
            ->setIdArticle($article->getId());

            $comment = New Comment();

            $form = $this->createForm(CommentType::class, $comment);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $comment->setCreatedAt(new \DateTime('now'))
                        ->setUser($article->getUser())
                        ->setArticle($article);  

                $manager->persist($comment);
                $manager->flush();

                $this->addFlash(
                    'successComment',
                    'Votre commentaire à bien été pris en compte !'
                );

                return $this->redirectToRoute('article_show', [
                    'id' => $article->getId()
                ]);
            }

            $images = $manager
            ->getRepository(Image::class)
            ->findByArticle($article);

            $contents = $manager
            ->getRepository(Content::class)
            ->findByArticle($article);

            return $this->render('home/articleShow.html.twig', [
                'form' => $form->createView(),
                'article' => $article,
                'totalContent' => (count($images) + count($contents)),
                'images' => $images,
                'contents' => $contents,
                'pagination' => $pagination
            ]);
        }
    }    
}