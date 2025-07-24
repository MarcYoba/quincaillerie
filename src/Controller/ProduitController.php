<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        return $this->render('produit/produit.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
        ]);
    }
    #[Route('/produit/create', name: 'app_produit_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $filename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $filename);
                $produit->setImage($filename);
            }
            $produit->setUser($this->getUser());
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }
        return $this->render('produit/create.html.twig', [
            'controller_name' => 'ProduitController',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/produit/list', name: 'app_produit_list')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        return $this->render('produit/list.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
        ]);
    }
}
