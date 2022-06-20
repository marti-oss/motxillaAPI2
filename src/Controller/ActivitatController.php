<?php

namespace App\Controller;

use App\Entity\Activitat;
use App\Repository\ActivitatRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivitatController extends AbstractController
{
    /**
     * @Route("/activitats",methods={"GET"})
     */
    public function getActivitats(ActivitatRepository $repository)
    {
        $activitatArray = [];
        $activitats = $repository->findAll();
        foreach ($activitats as $activitat) {
            $activitatArray[] = [
                'id' => $activitat->getId(),
                'name' => $activitat->getNom(),
                'objectiu' => $activitat->getObjectiu(),
                'interior' => $activitat->isInterior(),
                'descripcio' => $activitat->getDescripcio()
            ];
        }
        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'data' => $activitatArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/activitats/{id}", name="activity_list")
     */
    public function getActivitat(int $id, ActivitatRepository $repository)
    {
        $response = new JsonResponse();
        $activitatArray = [];
        $activitat = $repository->find($id);
        if ($activitat == null) return $response->setData(['success' => false, 'description' => 'Activitat no existeix', 'code' => 401]);
        $activitatArray[] = [
            'id' => $activitat->getId(),
            'name' => $activitat->getNom(),
            'objectiu' => $activitat->getObjectiu(),
            'interior' => $activitat->isInterior(),
            'descripcio' => $activitat->getDescripcio()
        ];
        $response->setData([
            'success' => true,
            'data' => $activitatArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/activitats", methods={"POST"})
     */
    public function postActivitats(Request $request, ActivitatRepository $repository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $name = $request->get('name');
        $objectiu = $request->get('objectiu');
        $interior = $request->get('interior');
        $descripcio = $request->get('descripcio');
        if ($name == null) {
            return $response->setData(['success' => false, 'description' => 'Indicar name', 'code' => 401]);
        }
        if ($objectiu == null) {
            return $response->setData(['success' => false, 'description' => 'Indicar objectiu', 'code' => 401]);
        }
        if ($interior == null) {
            return $response->setData(['success' => false, 'description' => 'Indicar interior', 'code' => 401]);
        }
        $activitat = new Activitat();
        $activitat->setNom($name);
        $activitat->setObjectiu($objectiu);
        $activitat->setInterior($interior);
        $activitat->setDescripcio($descripcio);
        $entityManager = $doctrine->getManager();
        $repository->add($activitat);
        $entityManager->flush();

        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $activitat->getId(),
                'nom' => $activitat->getNom(),
                'objectiu' => $activitat->getObjectiu(),
                'interior' => $activitat->isInterior(),
                'descripcio' => $activitat->getDescripcio()
            ],
            'code' => 200
        ]);
    }
}