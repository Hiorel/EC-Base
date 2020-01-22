<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Content;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        // Ajout de l'administrateur
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        
        $adminUser = new User();
        $adminUser->setName("Hiorel")
                  ->setEmail("s.leclerc076@outlook.fr")
                  ->setAvatar("no_avatar.png")
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->addUserRole($adminRole);

        $manager->persist($adminUser);

        $users = [];
        for ($i=0; $i < 20; $i++) { 
            $user = new User();
            $user->setName($faker->firstname())
                  ->setEmail($faker->email)
                  ->setAvatar("no_avatar.png")
                  ->setHash($this->encoder->encodePassword($user, 'password'));

            $manager->persist($user);
            $users[] = $user;
        }
                 
            // Ajout des Types d'article

            $typeArt = new Type();
            $typeArt->setName('Lodestone');
            $manager->persist($typeArt);

            $typeArt2 = new Type();
            $typeArt2->setName('Guide');
            $manager->persist($typeArt2);

            // Ajout de faux Articles

            $repoType = [$typeArt, $typeArt2];

            for ($i=0; $i < 10; $i++) { 
                
                $newArticle = new Article();

                $typeTmp =  $repoType[mt_rand(0, 1)];
                if($typeTmp->getName() == 'Lodestone') {
                    $urlExt = $faker->url() ;
                }
                else {
                    $urlExt = NULL;
                }

                $newArticle->setTitle($faker->sentence())
                           ->setCover('img_article.jpg')
                           ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                           ->setType($typeTmp)
                           ->setUrlExt($urlExt)
                           ->setUser($adminUser);

                $orderArt = 0;           
                for ($j=0; $j <= mt_rand(2,5); $j++) {
                    $orderArt++;

                    $image = new Image();
                        
                    $image->setUrl('http://placekitten.com/600/350')
                          ->setCaption($faker->sentence())
                          ->setOrderArt($orderArt)
                          ->setArticle($newArticle);

                    $manager->persist($image);

                    $orderArt++;

                    $content = new Content();
                        
                    $content->setContentArt($faker->paragraph())
                            ->setOrderArt($orderArt)
                            ->setArticle($newArticle);

                    $manager->persist($content);
                }

                for ($k=0; $k < count($users); $k++) { 
                    if(mt_rand(0,1)) { 
                        $comment = new Comment();
                        $comment->setContent($faker->paragraph())
                                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                                ->setUser($users[$k])
                                ->setArticle($newArticle);

                        $manager->persist($comment);
                    }
                }
                $manager->persist($newArticle);
            }
         $manager->flush();
    }
}
