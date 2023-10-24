<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostformType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class PostController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/post', name: 'post')]
    public function index(Request $request): Response
    {
    
        $post = new Post();
        $form =  $this->createForm(PostformType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez l'entité dans la base de données
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            // Redirigez l'utilisateur vers une autre page après la création de l'article
            return $this->redirectToRoute('all_posts'); // Redirige vers la même page pour un nouvel article, vous pouvez modifier ceci selon vos besoins
        }

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/all_posts', name: 'all_posts')]
    public function allPosts(Request $request): Response
    {
        $posts = $this->entityManager->getRepository(Post::class)->findAll();

        return $this->render('post/all_posts.html.twig', [
            'posts' => $posts,
        ]);
    }
    #[Route('/edit_post/{id}', name: 'edit_post')]
    public function editPost(Request $request, $id): Response
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        $form = $this->createForm(PostformType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('all_posts');
        }

        return $this->render('post/edit_post.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/delete_post/{id}', name: 'delete_post')]
    public function deletePost(Request $request, $id): Response
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return $this->redirectToRoute('all_posts');
    }
    
}
