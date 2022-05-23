<?php

namespace App\Controller;

use App\Repository\ActivitatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivitatController extends AbstractController
{
    /**
     * @Route("/activitats")
     */

    public function getActivitats(Request $request, ActivitatRepository $repository){
        $id = $request->get('id');
        $activitatArray = [];
        if($id != null){
            $activitat = $repository->find($id);
            $activitatArray[] = [
                'id' => $activitat->getId(),
                'name' => $activitat->getNom(),
                'monitors' => $activitat -> getObjectiu(),
                'participants' => $activitat -> isInterior(),
                'descripcio' => $activitat -> getDescripcio()
            ];
        } if ($id == null) {
            $activitats = $repository->findAll();
            foreach($activitats as $activitat) {
                $activitatArray[] = [
                    'id' => $activitat->getId(),
                    'name' => $activitat->getNom(),
                    'monitors' => $activitat -> getObjectiu(),
                    'participants' => $activitat -> isInterior(),
                    'descripcio' => $activitat -> getDescripcio()
                ];
            }
        }
        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'data' => $activitatArray
        ]);
        return $response;
    }

    /**
     * @Route("/activitats/{id}", name="activity_list")
     */

    public function getActivitat(int $id,ActivitatRepository $repository){
        $response = new JsonResponse();
        $activitatArray = [];
        $activitat = $repository->find($id);
        if ($activitat == null) return $response -> setData(['success'=>false, 'description'=>'Activitat no existeix']);
        $activitatArray[] = [
            'id' => $activitat->getId(),
            'name' => $activitat->getNom(),
            'monitors' => $activitat -> getObjectiu(),
            'participants' => $activitat -> isInterior(),
            'descripcio' => $activitat -> getDescripcio()
        ];
        $response->setData([
            'success' => true,
            'data' => $activitatArray
        ]);
        return $response;
    }

}