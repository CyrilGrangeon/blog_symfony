<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Exception;

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

    #[Route('/nouvel-article', name: 'article-new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $slug = $slugger->slug($article->getTitle().'-'.rand(100, 500)) ;
            $article->setSlug($slug);

            $em->persist($article);

            try{
                $em->flush($article);
            }catch(Exception $e){
                
                return $this->redirectToRoute('article-new');
            }
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('article/new.html.twig', [ 
            'form' => $form->createView()
        ]);
    }
    #[Route('/article/{slug}', name: 'article_show')]
    public function show(ArticleRepository $repo, $slug): Response
    {
        $article = $repo->findOneBy(['slug' => $slug]);
        return $this->render('article/show.html.twig', [ 
            'article' => $article
        ]);
    }

    #[Route('/supprimer-article/{slug}', name: 'article_delete')]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
       $em->remove($article);
       $em->flush();

       return $this->redirectToRoute('article_list');
    }
    #[Route('/modifier-article/{slug}', name: 'article_edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
       $form = $this->createForm(ArticleType::class, $article);
       $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article->setUpdatedAt(new \DateTime);
            $em->flush();
            return $this->redirectToRoute('article_list');
        }
       return $this->render('article/edit.html.twig', [
           'form' => $form->createView()
       ]);
    }
}
