<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/liste-des-articles', name: 'article_list')]
    public function list(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('article/list.html.twig', [ 
            'articles' => $articles
        ]);
    }
}
