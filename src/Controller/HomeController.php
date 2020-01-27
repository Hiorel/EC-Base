<?php

namespace App\Controller;

use DateTime;
use App\Entity\Type;
use App\Entity\Image;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Content;
use App\Form\CommentType;
use App\Repository\TypeRepository;
use App\Service\PaginationService;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="home")
     */
    public function index( $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Article::class)
        ->setLimit(8)
        ->setPage($page);

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/tools", name="tools")
     */
    public function tools()
    {
        return $this->render('home/tools.html.twig');
    }

    /**
     * @Route("/videos", name="cat_videos")
     */
    public function videos()
    {
        return $this->render('home/videos.html.twig');
    }

    /**
     * @Route("/screenshots", name="cat_screens")
     */
    public function screens()
    {
        return $this->render('home/screens.html.twig');
    }

    /**
     * @Route("/cat-article/{name}/{page<\d+>?1}", name="cat_article")
     */
    public function catArticle(Type $type, ArticleRepository $repo, TypeRepository $repoType, $page, PaginationService $pagination)
    {
        $nameType = ($repoType->findOneById($type->getId()))->getName();

        $pagination->setEntityClass(Article::class)
        ->setLimit(8)
        ->setTemplatePath('partials/paginationAlt3.html.twig')
        ->setPage($page)
        ->setNameType($nameType)
        ->setIdType($type->getId());

        return $this->render('home/catArticle.html.twig', [
            'name' => $nameType,
            'pagination' => $pagination
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