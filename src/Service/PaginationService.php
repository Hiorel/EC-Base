<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class PaginationService {
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private $entityClass;

    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var integer
     */
    private $currentPage = 1;

    /**
     * Le manager de Doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Twig\Environment
     */
    private $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de la navigation
     *
     * @var string
     */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $templatePath;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $idUser;

        /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $idArticle;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     * 
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, string $templatePath) {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');        
        // Autres initialisations
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     * 
     * @return void
     */
    public function display() {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

        /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     * 
     * @return void
     */
    public function display2() {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages2(),
            'route' => $this->route,
            'idUser' => $this->getIdUser()
        ]);
    }

            /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     * 
     * @return void
     */
    public function display3() {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages3(),
            'route' => $this->route,
            'idArticle' => $this->getIdArticle()
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     * 
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * 
     * @return int
     */
    public function getPages(): int {
        if(empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaitre le total des enregistrements de la table
        $total = count($this->manager
                        ->getRepository($this->entityClass)
                        ->findAll());

        // 2) Faire la division, l'arrondi et le renvoyer
        return ceil($total / $this->limit);
    }

    /**
     * Même que GetPages mais avec une requete différente
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * 
     * @return int
     */
    public function getPages2(): int {
        if(empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaitre le total des enregistrements de la table
        $total = count($this->manager
                        ->getRepository($this->entityClass)
                        ->findByUser($this->idUser));

        // 2) Faire la division, l'arrondi et le renvoyer
        return ceil($total / $this->limit);
    }

        /**
     * Même que GetPages mais avec une requete différente / Commentaire sur article
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * 
     * @return int
     */
    public function getPages3(): int {
        if(empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaitre le total des enregistrements de la table
        $total = count($this->manager
                        ->getRepository($this->entityClass)
                        ->findByArticle($this->idArticle));

        // 2) Faire la division, l'arrondi et le renvoyer
        return ceil($total / $this->limit);
    }





    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * 
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une 
     * certaine limite et en partant d'un offset
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les éléments à partir d'un offset et 
        // dans la limite d'éléments imposée (voir propriété $limit)
        return $this->manager
                        ->getRepository($this->entityClass)
                        ->findBy([], [], $this->limit, $offset);
    }


        /**
     * Pareil que GetData mais avec une requete spécifique
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData2() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les éléments à partir d'un offset et 
        // dans la limite d'éléments imposée (voir propriété $limit)
        return $this->manager
                        ->getRepository($this->entityClass)
                        ->findBy(array('user' => $this->idUser), [], $this->limit, $offset);
    }

    /**
     * Pareil que GetData mais avec une requete spécifique / Commentaire sur article
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData3() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les éléments à partir d'un offset et 
        // dans la limite d'éléments imposée (voir propriété $limit)
        return $this->manager
                        ->getRepository($this->entityClass)
                        ->findBy(array('article' => $this->idArticle), array('createdAt' => 'desc'), $this->limit, $offset);
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param int $page
     * @return self
     */
    public function setPage(int $page): self {
        $this->currentPage = $page;

        return $this;
    }

    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return int
     */
    public function getPage(): int {
        return $this->currentPage;
    }

    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self {
        $this->limit = $limit;

        return $this;
    }

        /**
     * Permet de récupérer l'id de l'utilisateur actuellement choisi
     *
     * @return int
     */
    public function getIdUser(): int {
        return $this->idUser;
    }

    /**
     * Permet de spécifier l'utilisateur actuellement choisi'
     *
     * @param int $idUser
     * @return self
     */
    public function setIdUser(int $idUser): self {
        $this->idUser = $idUser;

        return $this;
    }

    
        /**
     * Permet de récupérer l'id de l'utilisateur actuellement choisi
     *
     * @return int
     */
    public function getIdArticle(): int {
        return $this->idArticle;
    }

    /**
     * Permet de spécifier l'utilisateur actuellement choisi'
     *
     * @param int $idUser
     * @return self
     */
    public function setIdArticle(int $idArticle): self {
        $this->idArticle = $idArticle;

        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrements qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int {
        return $this->limit;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     * - App\Entity\Ad::class
     * - App\Entity\Comment::class
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass(string $entityClass): self {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass(): string {
        return $this->entityClass;
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     * @return self
     */
    public function setTemplatePath(string $templatePath): self {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */ 
    public function getTemplatePath(): string {
        return $this->templatePath;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param string $route Le nom de la route à utiliser
     * @return self
     */
    public function setRoute(string $route): self {
        $this->route = $route;

        return $this;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisé sur les liens de la navigation
     *
     * @return string
     */
    public function getRoute(): string {
        return $route;
    }
}