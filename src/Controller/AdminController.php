<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * Permet la création d'un article
     * 
     * @Route("/admin/create-article", name="admin_create_article")
     * 
     * @return Response
     */
    public function createArticle()
    {
        return $this->render('admin/createArticle.html.twig');
    }

    /**
     * Permet l'édition et la suppression d'un article
     * 
     * @Route("/admin/edit-article", name="admin_edit_article")
     * 
     * @return Response
     */
    public function editArticle(EntityManagerInterface $manager)
    {
        $articles = $manager->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.createdAt DESC')->getResult();

        return $this->render('admin/editArticle.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * Permet l'édition d'un article en particulier
     *
     * @Route("/admin/edit-article/{id}", name="admin_edit_one_article")
     * 
     * @return Response
     */
    public function editOneArticle(Article $article) {
        return $this->render('admin/editOneArticle.html.twig', [
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
     * @Route("/admin/user-management", name="admin_user_management")
     *
     * @return void
     */
    public function userManagement (EntityManagerInterface $manager) {

        $users = $manager->createQuery('SELECT u FROM App\Entity\User u')->getResult();

        return $this->render('admin/userManagement.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Permet l'édition d'un utilisateur en particulier
     *
     * @Route("/admin/edit-user/{id}", name="admin_edit_one_user")
     * 
     * @return Response
     */
    public function editOneUser(User $user) {
        return $this->render('admin/editOneUser.html.twig', [
            'user' => $user
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
}
