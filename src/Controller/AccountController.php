<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\AccountType;
use App\Form\CommentType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)    {
        /*if(!($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'))){
            return $this->redirectToRoute('account_profile');
        }*/

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout() {

    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
            
            if($form['avatar']->getData()) {
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
                $user->setAvatar('no_avatar.png');
            }

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée ! Vous pouvez vous connecter !'
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();
        $avatarUser = $this->getUser()->getAvatar();
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

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     * 
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Vérifier que le oldpassword soit la même que la password de l'user
           if(!(password_verify($passwordUpdate->getOldPassword(), $user->getHash()))){
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
           }else{
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
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet l'édition d'un commentaire
     *
     * @Route("/account/edit-comment/{idArt}/{id}", name="account_edit_one_comment")
     * 
     * @Security("is_granted('ROLE_ADMIN') or user === comment.getUser()", message="Ce commentaire ne vous appartient pas, vous ne pouvez pas le modifier")
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
                "Votre commentaire a bien été modifié !"
            );
        }
        
        return $this->render('account/editOneComment.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }
}
