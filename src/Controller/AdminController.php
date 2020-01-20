<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Service\PaginationService;
use App\Form\PasswordUpdateTypeAdmin;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/admin/edit-article/{page<\d+>?1}", name="admin_edit_article")
     * 
     * @return Response
     */
    public function editArticle(ArticleRepository $repo, EntityManagerInterface $manager, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Article::class)
                   ->setCurrentPage($page);

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
     * @Route("/admin/user-management/{page<\d+>?1}", name="admin_user_management")
     *
     * @return void
     */
    public function userManagement (EntityManagerInterface $manager, $page, PaginationService $pagination) {

        $pagination->setEntityClass(User::class)
                   ->setCurrentPage($page);

        return $this->render('admin/userManagement.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet l'édition d'un utilisateur en particulier
     *
     * @Route("/admin/edit-user/{id}", name="admin_edit_one_user")
     * 
     * @return Response
     */
    public function editOneUser(Request $request, EntityManagerInterface $manager,User $user) {
        $avatarUser = $user->getAvatar();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            if($form['avatar']->getData() !== $user->getAvatar()) {
                $brochureFile = $form['avatar']->getData();
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $brochureFile->move(
                            $this->getParameter('avatar_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $user->setAvatar($newFilename);
                }
            } else {
                
                $user->setAvatar($avatarUser);
            }
                
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont été modifiée avec succès !"
            );
        }

        return $this->render('admin/editOneUser.html.twig', [
            'form' => $form->createView(),
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
}
