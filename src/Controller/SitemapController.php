<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {
        // Nous récupérons le nom d'hôte depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();

        // On initialise un tableau pour lister les URLs
        $urls = [];

        // On ajoute les URLs "statiques"
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('tools')];
        $urls[] = ['loc' => $this->generateUrl('cat_videos')];
        $urls[] = ['loc' => $this->generateUrl('cat_screens')];
        $urls[] = ['loc' => $this->generateUrl('account_login')];
        $urls[] = ['loc' => $this->generateUrl('account_register')];

        // On ajoute les URLs dynamiques des articles dans le tableau
        foreach ($this->getDoctrine()->getRepository(Article::class)->findAll() as $article) {
            if ($article->getType()->getName() != "Lodestone")
            {
                $images = [
                    'loc' => '/uploads/articles/'.$article->getCover()
                ];

                $urls[] = [
                    'loc' => $this->generateUrl('article_show', [
                        'slug' => $article->getSlug(),
                    ]),
                    'lastmod' => $article->getCreatedAt()->format('Y-m-d'),
                    'image' => $images
                ];
            }
        }
        // Fabrication de la réponse XML
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls,
                'hostname' => $hostname]),
            200
        );

        // Ajout des entêtes
        $response->headers->set('Content-Type', 'text/xml');

        // On envoie la réponse
        return $response;
    }
}
