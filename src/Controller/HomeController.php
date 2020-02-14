<?php

namespace App\Controller;

use DateTime;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Videos;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Content;
use App\Form\CommentType;
use App\Entity\Screenshots;
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
     * Renvoie la page d'accueil du site
     * 
     * @Route("/{page<\d+>?1}", name="home")
     * 
     * @return Response
     */
    public function index( $page, PaginationService $pagination, ArticleRepository $articlesRepo )
    {
        $pagination->setEntityClass(Article::class)
        ->setLimit(8)
        ->setPage($page);

        $articles = $articlesRepo->findBy(array('type' => '1'),array('id' => 'desc'),4,0);


        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'name' => "Lodestone",
            'articles' => $articles
        ]);
    }

    /**
     * Renvoie la page des outils
     * 
     * @Route("/tools", name="tools")
     * 
     * @return Response
     */
    public function tools()
    {
        return $this->render('home/tools.html.twig');
    }

    /**
     * Retourne la page des guides vidéos
     * 
     * @Route("/videos/{page<\d+>?1}", name="cat_videos")
     * 
     * @return Response
     */
    public function videos($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Videos::class)
        ->setLimit(8)
        ->setPage($page);

        return $this->render('home/videos.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Retourne la page des screenshots
     * 
     * @Route("/screenshots/{page<\d+>?1}", name="cat_screens")
     * 
     * @return Response
     */
    public function screens($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Screenshots::class)
        ->setLimit(8)
        ->setPage($page);

        return $this->render('home/screens.html.twig', [
            'pagination' => $pagination
            ]);
    }

    /**
     * Retourne les différentes page de guide selon leur catégorie
     * 
     * @Route("/categorie/{name}/{page<\d+>?1}", name="cat_article")
     * 
     * @return Response
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
    * @Route("/article/{slug}/{page<\d+>?1}", name="article_show")
    * 
    * @return Response
    */
    public function showArticle(Request $request, EntityManagerInterface $manager, Article $article, $page, PaginationService $pagination) {
        if($article->getType()->getName() == "Lodestone")
        {
            return $this->redirect($article->getUrlExt());
        }else{ 
            $pagination->setEntityClass(Comment::class)
            ->setPage($page)
            ->setTemplatePath('partials/paginationAlt2.html.twig')
            ->setIdArticle($article->getId())
            ->setSlugArticle($article->getSlug());

            $comment = New Comment();

            $form = $this->createForm(CommentType::class, $comment);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $comment->setCreatedAt(new \DateTime('now'))
                        ->setUser($this->getUser())
                        ->setArticle($article);  

                $manager->persist($comment);
                $manager->flush();

                $this->addFlash(
                    'successComment',
                    'Votre commentaire à bien été pris en compte !'
                );

                return $this->redirectToRoute('article_show', [
                    'slug' => $article->getSlug()
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