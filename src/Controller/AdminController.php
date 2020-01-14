<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function editArticle()
    {
        return $this->render('admin/editArticle.html.twig');
    }

    /**
     * Permet de gérer les utilisateurs
     * 
     * @Route("/admin/user-management", name="admin_user_management")
     *
     * @return void
     */
    public function userManagement () {
        return $this->render('admin/userManagement.html.twig');
    }
}
