<?php

namespace App\Controller;

use App\Entity\Persona;
use App\Entity\Responsable;
use App\Repository\ParticipantRepository;
use App\Repository\ResponsableRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResponsableController extends AbstractController
{
    /**
     * @Route("/responsables", methods={"POST"})
     */
    public function postResponsable(Request $request, ResponsableRepository $responsableRepository,
                                    ParticipantRepository $participantRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $idPart = $request->get('idParticipant');
        $participant = $participantRepository->find($idPart);
        if($participant == null) return $response->setData(['succes'=>false, 'data'=>"Participant no trobat", 'code' => 401 ]);

        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $telefon1 = $request->get('telefon1');
        $telefon2 = $request->get('telefo2');
        $email = $request->get('email');

        if ($nom == null) {
            return $response->setData([
                'success' => false,
                'data' => 'Nom no indicat',
                'code' => 401
            ]);
        }
        if ($cognom1 == null) {
            return $response->setData([
                'success' => false,
                'data' => 'Cognom1 no indicat',
                'code' => 401
            ]);
        }
        if ($dni == null) {
            return $response->setData([
                'success' => false,
                'data' => 'dni no indicat',
                'code' => 401
            ]);
        }
        if ($email == null) {
            return $response->setData([
                'success' => false,
                'data' => 'email no indicat',
                'code' => 401
            ]);
        }
        if ($telefon1 == null) {
            return $response->setData([
                'success' => false,
                'data' => 'telefon1 no indicat',
                'code' => 401
            ]);
        }


        $responsable = new Responsable();
        $persona = new Persona();
        $persona = new Persona();
        $persona->setNom($nom);
        $persona->setCognom1($cognom1);
        $persona->setCognom2($cognom2);
        $persona->setDNI($dni);
        $responsable->setPersona($persona);
        $responsable->setEmail($email);
        $responsable->setTelefon1($telefon1);
        $responsable->setTelefon2($telefon2);
        $responsable->setParticipant($participant);
        $entityManager = $doctrine->getManager();
        $responsableRepository->add($responsable);
        $entityManager->flush();


        return $response->setData([
           'success'=>true,
           'data' => [
               'id'=> $responsable->getId(),
               'nom' => $responsable->getPersona()->getNom(),
               'cognom1' => $responsable->getPersona()->getCognom1(),
               'cognom2' => $responsable->getPersona()->getCognom2(),
               'dni' => $responsable->getPersona()->getDNI(),
               'email' => $responsable->getEmail(),
               'telefon1' => $responsable->getTelefon1(),
               'telefon2' => $responsable->getTelefon2(),
               'participantNom' => $responsable->getParticipant()->getPersona()->getNom()
           ],
            'code' => 200
        ]);
    }

}