<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MonitorRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminUserController extends AbstractController
{
    /*
    /**
     * @Route("/admin",methods={"POST"})
     */

/*
    public function getParticipants(UserRepository $userRepository, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new JsonResponse();
        $user = new User();
        $user->setEmail("motxillatfg@gmail.com");
        $plaintextPassword = "silverio7";
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $entityManager = $doctrine->getManager();
        $userRepository->add($user);
        $entityManager->flush();

        $response->setData(['data' => 'succes', 'content' => [
            'user' => $user->getEmail(),
            'password' => $user->getPassword()
        ]]);
        return $response;
    }
*/
    /**
     * @Route("/contrasenya", methods={"POST"})
     */
    public function canviarContrasenya(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine, Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new JsonResponse();
        //$user = $userRepository->find($security->getUser());
        $user = $userRepository->find(5);
        $cont_actual = $request->get("actual");
        $cont_nova = $request->get('nova');
        if ($cont_actual == null) return $response->setData(['success' => false, 'description' => 'actual no indicat',  'code' => 401]);
        if ($cont_nova == null) return $response->setData(['success' => false, 'description' => 'nova no indicat',  'code' => 401]);
        $plaintextPassword = $cont_nova;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $entityManager = $doctrine->getManager();
        $userRepository->add($user);
        $entityManager->flush();

        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function getUsers(Request $request, UserRepository $userRepository)
    {
        $response = new JsonResponse();
        $users = $userRepository->findAll();
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'contrasenya' => $user->getPassword(),
            ];
        }


        return $response->setData([
            'success' => true,
            'data' => $usersArray,
            'code' => 200
        ]);
    }

    /**
     * @Route("/tokenid", methods={"GET"})
     */
    public function getId(ManagerRegistry $doctrine, Security $security, UserRepository $userRepository, Connection $connection){
        $user = $userRepository->find($security->getUser());
        $id = $user->getId();
        $monitorid = $connection->createQueryBuilder()
            ->select('m.id')
            ->from('monitor', 'm')
            ->where('user_id = :id')
            ->setParameter('id',$id)
            ->executeQuery()
            ->fetchAllAssociative();
        $response = new JsonResponse();
        if (count($monitorid) != 0) $response->setData(['succes' => false, 'data' => 'Aquest usuari no és monitor', 'code' => 401]);
        return $response->setData([
            'success' => true,
            'data' => $monitorid ,
            'code' => 200
        ]);
    }


}