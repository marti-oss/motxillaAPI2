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

    /**
     * @Route("/activitatsprogramades/{id}", methods={"PUT"})
     */
    public function editActivitatProgramada(Request $request, int $id, ActivitatProgramadaRepository $activitatProgramadaRepository, ManagerRegistry $doctrine) {
        $response = new JsonResponse();
        $activitatProgramada = $activitatProgramadaRepository->find($id);
        if ($activitatProgramada == null) return $response -> setData(['success'=>false, 'description'=>'Activitat programada no existeix',  'code' => 401]);
        $dataIni = $request->get('dataIni');
        $dataFi = $request->get('dataFi');
        $nom = $request->get('nom');
        $objectiu = $request->get('objectiu');
        $interior = $request->get('interior');
        $descripcio = $request->get('descripcio');
        if ($activitatProgramada->getDataIni() > $activitatProgramada->getDataFi())
            return $response->setData(['success' => false, 'data' => 'dataIni posterior a dataFi',  'code' => 401]);

        if ($nom != null)
            $activitatProgramada->setNom($nom);
        if ($dataIni != null) {
            $timeIni = new \DateTime($dataIni);
            $activitatProgramada->setDataIni($timeIni);
        }
        if ($dataFi != null) {
            $timeFi = new \DateTime($dataFi);
            $activitatProgramada->setDataFi($timeFi);
        }
        if ($descripcio != null)
            $activitatProgramada->setDescripcio($descripcio);
        if ($objectiu != null)
            $activitatProgramada->setObjectiu($objectiu);
        if ($interior != null)
            $activitatProgramada->setInterior($interior);


        $entityManager = $doctrine->getManager();
        $activitatProgramadaRepository->add($activitatProgramada);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => 'Editat correctament',
            'code' => 200
        ]);
        return $response;
    }

}