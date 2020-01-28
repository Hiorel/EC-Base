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
use App\Form\VideosType;
use App\Form\AccountType;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\Screenshots;
use App\Form\LodestoneType;
use App\Service\FileService;
use App\Form\ScreenshotsType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Service\PaginationService;
use App\Form\PasswordUpdateTypeAdmin;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * Page du choix d'article
     *
     * @Route("/admin/create-type-article", name="admin_create_type_article")
     * 
     * @return void
     */
    public function createTypeArticle() {
        return $this->render('admin/createTypeArticle.html.twig');
    }

    /**
     * Permet la création d'un article
     * 
     * @Route("/admin/create-article", name="admin_create_article")
     * 
     * @return Response
     */
    public function createArticle(Request $request, EntityManagerInterface $manager, FileService $fileServ)
    {
        $article = new Article();
        
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
           
            $file = $form['cover']->getData();
            if ($file) {
                $newFilename = $fileServ->fileManagement($file); 
                
                try {
                    $file->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $article->setCover($newFilename);
            }

            foreach($article->getImages() as $image){
                $image->setArticle($article);
                $manager->persist($image);
            }

            foreach($article->getContents() as $content){
                $content->setArticle($article);
                $manager->persist($content);
            }

            $article->setCreatedAt(new \DateTime('now'));
            $article->setUser($this->getUser());

            $manager->persist($article);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'article à bien été créer avec succès !"
            );

            return $this->redirectToRoute('article_show', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('admin/createArticle.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet la création d'un article Lodestone
     * 
     * @Route("/admin/create-lodestone", name="admin_create_lodestone")
     * 
     * @return Response
     */
    public function createLodestone(Request $request, EntityManagerInterface $manager, FileService $fileServ)
    {
        $article = new Article();
        
        $form = $this->createForm(LodestoneType::class, $article);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
           
            $file = $form['cover']->getData();
            if ($file) {
                $newFilename = $fileServ->fileManagement($file);
                try {
                    $file->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $article->setCover($newFilename);
            }

            $article->setCreatedAt(new \DateTime('now'));

            $typeArt = $manager
            ->getRepository(Type::class)
            ->findOneByName('Lodestone');

            $article->setType($typeArt);
            $article->setUser($this->getUser());

            $manager->persist($article);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'article à bien été créer avec succès !"
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('admin/createLodestone.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet l'édition d'un article lodestone 
     * 
     * @Route("/admin/edit-article-lodestone/{id}", name="admin_edit_lodestone")
     * 
     * @return Response
     */
    public function editLodestone(Request $request, Article $article, EntityManagerInterface $manager, FileService $fileServ) {

        $coverArt = $article->getCover();
        $form = $this->createForm(LodestoneType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form['cover']->getData() !== $article->getCover()) {
                $file = $form['cover']->getData();
                if ($file) {
                    $newFilename = $fileServ->fileManagement($file);
                    try {
                        $file->move(
                            $this->getParameter('covers_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {}

                    $article->setCover($newFilename);
                }
            } else {$article->setCover($coverArt);
            }
                $manager->persist($article);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Les données de l'article ont été modifiée avec succès !"
                );

                return $this->redirectToRoute('admin_edit_article');
        }

        return $this->render('admin/editLodestone.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * Permet l'édition et la suppression d'un article
     * 
     * @Route("/admin/edit-article/{page<\d+>?1}", name="admin_edit_article")
     * 
     * @return Response
     */
    public function editArticle(ArticleRepository $repo, EntityManagerInterface $manager, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Article::class)
                   ->setPage($page);

        return $this->render('admin/editArticle.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet l'édition d'un article en particulier
     *
     * @Route("/admin/edit-article/{id}/edit", name="admin_edit_one_article")
     * 
     * @return Response
     */
    public function editOneArticle(Request $request, Article $article, EntityManagerInterface $manager, FileService $fileServ) {
        
        $coverArt = $article->getCover();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form['cover']->getData() !== $article->getCover()) {
                $file = $form['cover']->getData();
                if ($file) {
                    $newFilename = $fileServ->fileManagement($file);
                    try {
                        $file->move(
                            $this->getParameter('covers_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {}

                    $article->setCover($newFilename);
                }
            } else {$article->setCover($coverArt);}

            foreach($article->getImages() as $image){
                $image->setArticle($article);
                $manager->persist($image);
            }

            foreach($article->getContents() as $content){
                $content->setArticle($article);
                $manager->persist($content);
            }

                $manager->persist($article);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Les données de l'article <strong>{$article->getTitle()}</strong> ont été modifiée avec succès !"
                );

                return $this->redirectToRoute('admin_edit_article');
        }

        return $this->render('admin/editOneArticle.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * Supprimer l'article en variable
     * 
     * @Route("/admin/remove-article/{id}", name="admin_remove_article")
     *
     * @return Response
     */
    public function removeOneArticle(EntityManagerInterface $manager, Article $article) {
        $manager->remove($article);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$article->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_edit_article');
    }

    /**
     * Permet de gérer les utilisateurs
     * 
     * @Route("/admin/user-management/{page<\d+>?1}", name="admin_user_management")
     *
     * @return void
     */
    public function userManagement (EntityManagerInterface $manager, $page, PaginationService $pagination) {

        $pagination->setEntityClass(User::class)
                   ->setPage($page);

        return $this->render('admin/userManagement.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet l'édition d'un utilisateur en particulier
     *
     * @Route("/admin/edit-user/{id}/{page<\d+>?1}", name="admin_edit_one_user")
     * 
     * @return Response
     */
    public function editOneUser(Request $request, EntityManagerInterface $manager,User $user, $page, PaginationService $pagination, FileService $fileServ) {
        $pagination->setEntityClass(Comment::class)
                   ->setPage($page)
                   ->setTemplatePath('partials/paginationAlt.html.twig')
                   ->setIdUser($user->getId());
        
        $avatarUser = $user->getAvatar();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            if($form['avatar']->getData() !== $user->getAvatar()) {
                $file = $form['avatar']->getData();
                if ($file) {
                    $newFilename = $fileServ->fileManagement($file);
                    try {
                        $file->move(
                            $this->getParameter('avatar_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {}

                    $user->setAvatar($newFilename);
                }
            } else {$user->setAvatar($avatarUser);}
                
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont été modifiée avec succès !"
            );
        }

        return $this->render('admin/editOneUser.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'pagination' => $pagination
        ]);
    }

    /**
     * Supprimer l'utilisateur en variable
     * 
     * @Route("/admin/remove-user/{id}", name="admin_remove_user")
     *
     * @return Response
     */
    public function removeOneUser(EntityManagerInterface $manager, User $user) {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getName()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_user_management');
    }

    /**
     * Supprimer le commentaire en variable
     * 
     * @Route("/admin/remove-comment/{id}/{idUser}", name="admin_remove_comment")
     *
     * @return Response
     */
    public function removeOneComment(EntityManagerInterface $manager, Comment $comment, $idUser) {

        $articleId= $comment->getArticle();

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'successComment',
            "Le commentaire a bien été supprimée"
        );

        return $this->redirectToRoute('admin_edit_one_user', array('id' => $articleId));
    }

     /**
     * Supprimer le commentaire en variable
     * 
     * @Route("/admin/remove-comment-art/{id}/{idArticle}", name="admin_remove_comment_art")
     *
     * @return Response
     */
    public function removeOneCommentArt(EntityManagerInterface $manager, Comment $comment, $idArticle) {

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'successComment',
            "Le commentaire a bien été supprimée"
        );

        return $this->redirectToRoute('article_show', array('id' => $idArticle));
    }

     /**
     * Permet l'édition d'un commentaire
     *
     * @Route("/admin/edit-comment/{idUser}/{id}", name="admin_edit_one_comment")
     * 
     * @return Response
     */
    public function editOneComment(Request $request, EntityManagerInterface $manager, Comment $comment) {
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro {$comment->getId()} a bien été modifié !"
            );
        }
        
        return $this->render('admin/editOneComment.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/admin/password-update/{id}", name="admin_edit_password")
     * 
     * @return Response
     */
    public function updatePasswordUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, User $user) {
        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateTypeAdmin::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
               $newPassword = $passwordUpdate->getNewPassword();
               $hash = $encoder->encodePassword($user, $newPassword);

               $user->setHash($hash);

               $manager->persist($user);
               $manager->flush();

               $this->addFlash(
                'success',
                "Le mot de passe a bien été modifiée avec succès !"
            );
        }

        return $this->render('admin/passwordAdmin.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Page d'ajout de vidéos
     *
     * @Route("/admin/add-videos", name="admin_add_video")
     * 
     * @return Response
     */
    public function addVideos(Request $request, EntityManagerInterface $manager) {
        $video = new Videos();
        
        $form = $this->createForm(VideosType::class, $video);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($video);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre vidéo a bien été ajouté !'
            );
            return $this->redirectToRoute('cat_videos');
        }

        return $this->render('admin/addVideo.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Page d'édition de vidéos
     *
     * @Route("/admin/edit-videos/{id}", name="admin_edit_one_video")
     * 
     * @return Response
     */
    public function editVideos(Request $request, EntityManagerInterface $manager, Videos $video) {
        
        $form = $this->createForm(VideosType::class, $video);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($video);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre vidéo a bien été modifié !'
            );
            return $this->redirectToRoute('cat_videos');
        }

        return $this->render('admin/EditVideo.html.twig', [
            'form' => $form->createView(),
            'video' => $video
        ]);
    }

    /**
     * Supprimer la vidéo
     * 
     * @Route("/admin/remove-video/{id}", name="admin_remove_video")
     *
     * @return Response
     */
    public function removeVideo(EntityManagerInterface $manager, Videos $video) {

        $manager->remove($video);
        $manager->flush();

        $this->addFlash(
            'success',
            "La vidéo a bien été supprimé"
        );

        return $this->redirectToRoute('cat_videos');
    }

    /**
     * Page d'ajout des screens
     *
     * @Route("/admin/add-screen", name="admin_add_screen")
     * 
     * @return Response
     */
    public function addScreens(Request $request, EntityManagerInterface $manager) {
        $screen = new Screenshots();
        
        $form = $this->createForm(ScreenshotsType::class, $screen);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($screen);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre screenshot a bien été ajouté !'
            );
            return $this->redirectToRoute('cat_screens');
        }

        return $this->render('admin/addScreen.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Page d'édition des screens
     *
     * @Route("/admin/edit-screen/{id}", name="admin_edit_one_screen")
     * 
     * @return Response
     */
    public function editScreens(Request $request, EntityManagerInterface $manager, Screenshots $screen) {

        $form = $this->createForm(ScreenshotsType::class, $screen);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($screen);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre screenshot a bien été modifié !'
            );
            return $this->redirectToRoute('cat_screens');
        }

        return $this->render('admin/editScreen.html.twig', [
            'form' => $form->createView(),
            'screen' => $screen
        ]);
    }

     /**
     * Supprimer le screen
     * 
     * @Route("/admin/remove-screen/{id}", name="admin_remove_screen")
     *
     * @return Response
     */
    public function removeScreen(EntityManagerInterface $manager, Screenshots $screen) {

        $manager->remove($screen);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le screenshot a bien été supprimé"
        );

        return $this->redirectToRoute('cat_screens');
    }
}
