<?php

namespace App\Controller;


use Exception;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CategoryController extends AbstractController
{
    #[Route('/liste-des-category', name: 'category-list')]
    public function list(CategoryRepository $repo): Response
    {
        $categories = $repo->findAll();
        return $this->render('category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/categorie/{slug}', name: 'category-show')]
    public function show(CategoryRepository $repo, string $slug): Response
    {
        $category = $repo->findOneBy(['slug' => $slug]);
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/supprimer-categorie/{slug}', name: 'category-delete')]
    public function delete(EntityManagerInterface $em, Category $cat): Response
    {
        $em->remove($cat);
        $em->flush();

        return $this->redirectToRoute("category-list");
        
    }

    #[Route('/nouvelle-categorie', name: 'category-new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $slug = $slugger->slug($category->getName().'-'.rand(100, 500)) ;
            $category->setSlug($slug);

            $em->persist($category);

            try{
                $em->flush($category);
            }catch(Exception $e){
                
                return $this->redirectToRoute('category-new');
            }

            return $this->redirectToRoute('category-show', [
                'slug' => $slug
            ]);
        }
        
        return $this->render('category/new.html.twig', [ 
            'form' => $form->createView()
        ]);
        
        $em->flush();

        return $this->render('category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/modifier-category/{slug}', name: 'category-edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
       $form = $this->createForm(CategoryType::class, $category);
       $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $category->setSlug($category->getName().'-'.rand(100, 500));
            $em->flush();
            return $this->redirectToRoute('category-list');
        }
       return $this->render('category/edit.html.twig', [
           'form' => $form->createView()
       ]);
    }
}
