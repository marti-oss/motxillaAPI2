<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Persona;
use App\Repository\ParticipantRepository;
use App\Repository\ResponsableRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participants",methods={"GET"})
     */
    public function getParticipants(ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository)
    {
        $response = new JsonResponse();
        $participants = $participantRepository->findAll();
        $participantsArray = [];
        $responsables = $responsableRepository->findAll();

        foreach ($participants as $participant) {
            unset($responsableList);
            $responsableList = [];
            foreach ($responsables as $responsable) {
                if ($responsable->getParticipant()->getId() == $participant->getId())
                    $responsableList = [$participant];
            }
            $participantsArray = [
                'id' => $participant->getId(),
                'nom' => $participant->getPersona()->getNom(),
                'cognom1' => $participant->getPersona()->getCognom1(),
                'cognom2' => $participant->getPersona()->getCognom2(),
                'dni' => $participant->getPersona()->getDNI(),
                'autoritzacio' => $participant->isAutoritzacio(),
                'dataNaixement' => $participant->getDataNaixement(),
                'targetaSanitaria' => $participant->getTargetaSanitaria(),
                'responsables' => $responsableList
            ];
        }
        $response->setData([
            'success' => true,
            'data' => $participantsArray
        ]);
        return $response;
    }

    /**
     * @Route("/participants/{id}",methods={"GET"})
     */
    public function getParticipant(int $id, ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($id);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat']);
        $responsables = $responsableRepository->findAll();
        unset($responsableList);
        $responsableList = [];
        foreach ($responsables as $responsable) {
            if ($responsable->getParticipant()->getId() == $participant->getId())
                $responsableList = [$participant];
        }
        $participantsArray = [
            'id' => $participant->getId(),
            'nom' => $participant->getPersona()->getNom(),
            'cognom1' => $participant->getPersona()->getCognom1(),
            'cognom2' => $participant->getPersona()->getCognom2(),
            'dni' => $participant->getPersona()->getDNI(),
            'autoritzacio' => $participant->isAutoritzacio(),
            'dataNaixement' => $participant->getDataNaixement(),
            'targetaSanitaria' => $participant->getTargetaSanitaria(),
            'responsables' => $responsableList
        ];
        $response->setData([
            'success' => true,
            'data' => $participantsArray
        ]);
        return $response;
    }

    /**
     * @Route("/participants/{id}", methods={"DELETE"})
     */
    public function deleteParticipant(int $id, ParticipantRepository $participantRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($id);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat delete']);
        $entityManager = $doctrine->getManager();
        $participantRepository->remove($participant);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => "Esborrat correctament"
        ]);
        return $response;
    }

    /**
     * @Route("/participants", methods={"POST"})
     */
    public function addParticipants(Request $request, ParticipantRepository $participantRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $autoritzacio = $request->get('autoritzacio');
        $targetaSanitaria = $request->get('targetaSanitaria');
        $dataNeix = $request->get('dataNeixament');

        if ($nom == null) {
            return $response->setData([
                'success' => false,
                'data' => 'Nom no indicat'
            ]);
        }
        if ($cognom1 == null) {
            return $response->setData([
                'success' => false,
                'data' => 'Cognom1 no indicat'
            ]);
        }
        if ($dni == null) {
            return $response->setData([
                'success' => false,
                'data' => 'dni no indicat'
            ]);
        }
        if ($autoritzacio == null) {
            return $response->setData([
                'success' => false,
                'data' => 'autoritzacio no indicat'
            ]);
        }
        if ($targetaSanitaria == null) {
            return $response->setData([
                'success' => false,
                'data' => 'targetaSanitaria no indicat'
            ]);
        }
        if ($dataNeix == null) {
            return $response->setData([
                'success' => false,
                'data' => 'dataNeixament no indicat'
            ]);
        }

        $participant = new Participant();
        $persona = new Persona();
        $persona->setNom($nom);
        $persona->setCognom1($cognom1);
        $persona->setCognom2($cognom2);
        $persona->setDNI($dni);
        $participant->setPersona($persona);
        $participant->setAutoritzacio($autoritzacio);
        $participant->setTargetaSanitaria($targetaSanitaria);
        $time = new \DateTime($dataNeix);
        $participant->setDataNaixement($time);

        $entityManager = $doctrine->getManager();
        $participantRepository->add($participant);
        $entityManager->flush();

        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $participant->getId(),
                'nom' => $participant->getPersona()->getNom(),
                'cognom1' => $participant->getPersona()->getCognom1(),
                'cognom2' => $participant->getPersona()->getCognom2(),
                'dni' => $participant->getPersona()->getDNI(),
                'autoritzacio' => $participant->isAutoritzacio(),
                'targetaSanitaria' => $participant->getTargetaSanitaria(),
                'dataNeixament' => $participant->getDataNaixement()
            ]
        ]);
    }

    /**
     * @Route("/participants/{id}", methods={"PUT"})
     */
    public function editParticipant(int $id, Request $request, ParticipantRepository $participantRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($id);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor']);
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $autoritzacio = $request->get('autoritzacio');
        $targetaSanitaria = $request->get('targetaSanitaria');
        $dataNeix = $request->get('dataNeixament');
        if ($nom != null) {
            $participant->getPersona()->setNom($nom);
        }
        if ($cognom1 != null) {
            $participant->getPersona()->setCognom1($cognom1);
        }
        if ($cognom2 != null){
            $participant->getPersona()->setCognom2($cognom2);
        }
        if ($dni != null) {
            $participant->getPersona()->setDNI($cognom2);
        }
        if ($autoritzacio != null) {
            $participant->setAutoritzacio($autoritzacio);
        }
        if ($targetaSanitaria != null) {
            $participant->setTargetaSanitaria($targetaSanitaria);
        }
        if ($dataNeix != null) {
            $participant->setDataNaixement(new \DateTime($dataNeix));
        }
        $entityManager = $doctrine->getManager();
        $participantRepository->add($participant);
        $entityManager->flush();
        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $participant->getId(),
                'nom' => $participant->getPersona()->getNom(),
                'cognom1' => $participant->getPersona()->getCognom1(),
                'cognom2' => $participant->getPersona()->getCognom2(),
                'dni' => $participant->getPersona()->getDNI(),
                'autoritzacio' => $participant->isAutoritzacio(),
                'targetaSanitaria' => $participant->getTargetaSanitaria(),
                'dataNeixament' => $participant->getDataNaixement()
            ]
        ]);
    }


}