<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use PhpParser\Node\Stmt\Return_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $user = $entityManager->getRepository(User::class)->find($user);
            if ($user->getLastLogin() === null) {
                $user->setLastLogin(new \DateTime());
                $entityManager->flush();
            }else{
                if ($user->getLastLogin() < new \DateTime('-6 hours')) {
                    $user->setLastLogin(new \DateTime());
                    $entityManager->flush();
                    //return $this->redirectToRoute('app_logout');
                }
            }
            return $this->redirectToRoute('app_home');

        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(EntityManagerInterface $entityManager): void
    {
        $user = $this->getUser();
        $user = $entityManager->getRepository(User::class)->find($user);
        if ($user) {
            $user->setLastLogin(NULL);
            $entityManager->flush();
        }
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/user/fotgot-password', name: "app_forgot")]
     
    public function fotgotPassword(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher) :Response {
        
        $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        $userExite = 0;
        if ($request->isMethod('POST') && $request->request->has('user')) {
            $reponse = $request->request->all();
            $email = $reponse['user']['email'];

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
                $userExite = 1;
                $this->addFlash('success', 'Utilisateur trouvé avec cet email.');
            } else {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
                $userExite = 0;
            }

            if (isset($reponse['user']['password']) && !empty($reponse['user']['password'])) {
                // Hachage et mise à jour du mot de passe
                $password = $reponse['user']['password'];
                $hashedPassword = $userPasswordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                
                $entityManager->flush();
    
                $this->addFlash('success', 'Mot de passe mis à jour avec succès !');
                return $this->redirectToRoute('app_login');
            }

        }
        return $this->render('security/fotgot_password.html.twig',[
            'form' => $form->createView(),
            'userExist' => $userExite,
        ]);
    }

    /**
     *@Route(path ="/user/list" , name="user_list")
     */
    public function list(EntityManagerInterface $entityManager) : Response {
        
       $user = $entityManager->getRepository(User::class)->findAll();
        return $this->render('security/list.html.twig',[
            'user' => $user,
        ]);
    }
    #[Route(path : '/user/edit/{id}' , name: 'user_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $id) : Response {
        
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('security/edit.html.twig',[
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route(path : '/user/delete/{id}' , name: 'user_delete')]
    public function delete(EntityManagerInterface $entityManager, User $user) : Response {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('user_list');
    }
}
