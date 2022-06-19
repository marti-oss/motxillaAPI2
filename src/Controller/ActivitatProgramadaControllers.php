<?php

namespace App\Controller;

use App\Repository\ActivitatProgramadaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivitatProgramadaControllers extends AbstractController
{
    /**
     * @Route("/activitatsprogramades/{id}", methods={"DELETE"})
     */
    public function deleteActivitatProgramada(int $id, ActivitatProgramadaRepository $activitatProgramadaRepository, ManagerRegistry $doctrine) {
        $response = new JsonResponse();
        $activitatProgramada = $activitatProgramadaRepository->find($id);
        if ($activitatProgramada == null) return $response -> setData(['success'=>false, 'description'=>'Activitat programada no existeix',  'code' => 401]);
        $entityManager = $doctrine->getManager();
        $activitatProgramadaRepository->remove($activitatProgramada);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => 'Esborrat correctament',
            'code' => 200
        ]);
        return $response;
    }

}