<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Freelancer;

#[Route('/api', name: 'api_')]
class FreelancerController extends AbstractController
{
    #[Route('/freelancers', name: 'freelancer_index', methods:['get'] )]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $freelancers = $doctrine
            ->getRepository(Freelancer::class)
            ->findAll();
   
        $data = [];
   
        foreach ($freelancers as $freelancer) {
           $data[] = [
               'id' => $freelancer->getId(),
               'username' => $freelancer->getUsername(),
                'email' => $freelancer->getEmail(),
                'password' => $freelancer->getPassword(),
                'country' => $freelancer->getCountry(),
                'phone' => $freelancer->getPhone(),
               'description' => $freelancer->getDescription(),
           ];
        }
        return $this->json($data);
    }
    #[Route('/freelancers', name: 'freelancer_create', methods:['post'] )]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $freelancers = $doctrine
            ->getRepository(Freelancer::class)
            ->findAll();
   
        foreach ($freelancers as $freelancer) {
           if($freelancer->getEmail()==$request->request->get('email') || $freelancer->getUsername()==$request->request->get('username')){
                return $this->json('Email or username already existe' , 404); 
           }
        }
        $freelancer = new Freelancer();
        $freelancer->setEmail($request->request->get('email'));
        $freelancer->setUsername($request->request->get('username'));
        $freelancer->setPassword($request->request->get('password'));
        $freelancer->setCountry($request->request->get('country'));
        $freelancer->setPhone($request->request->get('phone'));
        $freelancer->setDescription($request->request->get('description'));
   
        $entityManager->persist($freelancer);
        $entityManager->flush();
   
        $data =  [
                'id' => $freelancer->getId(),
               'username' => $freelancer->getUsername(),
                'email' => $freelancer->getEmail(),
                'password' => $freelancer->getPassword(),
                'country' => $freelancer->getCountry(),
                'phone' => $freelancer->getPhone(),
               'description' => $freelancer->getDescription(),
        ];
           
        return $this->json($data);
    }
    #[Route('/freelancers/login', name: 'freelancer_login', methods:['post'] )]
    public function login(ManagerRegistry $doctrine,Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        if($request->request->get('email') and $request->request->get('password')){
            $freelancer = $entityManager->getRepository(Freelancer::class)->findOneBy(['email' => $request->request->get('email')]);
            if(!$freelancer || $freelancer->getPassword()!=$request->request->get('password')){
                return $this->json('No freelancer found for this email and password' , 404);
            }
            $data = [
                'id' => $freelancer->getId(),
                'username' => $freelancer->getUsername(),
                'email' => $freelancer->getEmail(),
                'password' => $freelancer->getPassword(),
                'country' => $freelancer->getCountry(),
                'phone' => $freelancer->getPhone(),
                'description' => $freelancer->getDescription(),
            ];

            return $this->json($data);
        }
        return $this->json('email or password not send in body');
    }
    #[Route('/freelancers/{id}', name: 'freelancer_show', methods:['get'] )]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $freelancer = $doctrine->getRepository(Freelancer::class)->find($id);
   
        if (!$freelancer) {
            return $this->json('No freelancer found for id ' . $id, 404);
        }
   
        $data =  [
            'id' => $freelancer->getId(),
            'username' => $freelancer->getUsername(),
             'email' => $freelancer->getEmail(),
             'password' => $freelancer->getPassword(),
             'country' => $freelancer->getCountry(),
             'phone' => $freelancer->getPhone(),
            'description' => $freelancer->getDescription(),
        ];
           
        return $this->json($data);
    }
    #[Route('/freelancers/{id}', name: 'freelancer_update', methods: ['put', 'patch'])]
    public function update(ManagerRegistry $doctrine, int $id,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();
        $freelancer = $entityManager->getRepository(Freelancer::class)->find($id);
        if (!$freelancer) {
            return $this->json('No freelancer found for id ' . $id, 404);
        }
        if($request->request->get('email'))
            $freelancer->setEmail($request->request->get('email'));
        if($request->request->get('username'))
            $freelancer->setUsername($request->request->get('username'));
        if($request->request->get('password'))
            $freelancer->setPassword($request->request->get('password'));
        if($request->request->get('country'))
            $freelancer->setCountry($request->request->get('country'));
        if($request->request->get('phone'))
            $freelancer->setPhone($request->request->get('phone'));
        if($request->request->get('description'))
            $freelancer->setDescription($request->request->get('description'));
        $entityManager->flush();
        $data = [
            'id' => $freelancer->getId(),
            'username' => $freelancer->getUsername(),
            'email' => $freelancer->getEmail(),
            'password' => $freelancer->getPassword(),
            'country' => $freelancer->getCountry(),
            'phone' => $freelancer->getPhone(),
            'description' => $freelancer->getDescription(),
        ];

        return $this->json($data);
    }
    #[Route('/freelancers/{id}', name: 'freelancer_delete', methods:['delete'] )]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $freelancer = $entityManager->getRepository(Freelancer::class)->find($id);
   
        if (!$freelancer) {
            return $this->json('No freelancer found for id' . $id, 404);
        }
   
        $entityManager->remove($freelancer);
        $entityManager->flush();
   
        return $this->json('Deleted a freelancer successfully with id ' . $id);
    }
    
}
