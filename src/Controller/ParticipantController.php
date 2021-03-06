<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Persona;
use App\Entity\Responsable;
use App\Repository\ParticipantRepository;
use App\Repository\ResponsableRepository;
use Doctrine\DBAL\Connection;
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
    public function getParticipants(ParticipantRepository $participantRepository)
    {
        $response = new JsonResponse();
        $participants = $participantRepository->findAll();
        $participantsArray = [];

        foreach ($participants as $participant) {
            $participantsArray[] = [
                'id' => $participant->getId(),
                'nom' => $participant->getPersona()->getNom(),
                'cognom1' => $participant->getPersona()->getCognom1(),
                'cognom2' => $participant->getPersona()->getCognom2(),
                'dni' => $participant->getPersona()->getDNI(),
                'autoritzacio' => $participant->isAutoritzacio(),
                'dataNaixement' => $participant->getDataNaixement()->format("d/m/Y"),
                'targetaSanitaria' => $participant->getTargetaSanitaria(),
            ];
        }
        $response->setData([
            'success' => true,
            'data' => $participantsArray,
            'code' => 200
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
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat', 'code' => 401]);
        $participantsArray = [
            'id' => $participant->getId(),
            'nom' => $participant->getPersona()->getNom(),
            'cognom1' => $participant->getPersona()->getCognom1(),
            'cognom2' => $participant->getPersona()->getCognom2(),
            'dni' => $participant->getPersona()->getDNI(),
            'autoritzacio' => $participant->isAutoritzacio(),
            'dataNaixement' => $participant->getDataNaixement()->format("d/m/Y"),
            'targetaSanitaria' => $participant->getTargetaSanitaria(),
        ];
        $response->setData([
            'success' => true,
            'data' => $participantsArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/participants/{id}/responsables",methods={"GET"})
     */
    public function getResponsables(int $id, ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($id);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat', 'code' => 401]);
        $responsables = $responsableRepository->findAll();
        $responsableList = [];
        foreach ($responsables as $responsable) {
            if ($responsable->getParticipant()->getId() == $participant->getId())
                $responsableList[] = [
                    'id' => $responsable->getId(),
                    'nom' => $responsable->getPersona()->getNom(),
                    'cognom1' => $responsable->getPersona()->getCognom1(),
                    'cognom2' => $responsable->getPersona()->getCognom2(),
                    'dni' => $responsable->getPersona()->getDNI(),
                    'email' => $responsable->getEmail(),
                    'telefon1' => $responsable->getTelefon1(),
                    'telefon2' => $responsable->getTelefon2()
                ];
        }
        return $response->setData([
            'success' => true,
            'data' => $responsableList,
            'code' => 200
        ]);
    }

    /**
     * @Route("/participants/{id}", methods={"DELETE"})
     */
    public function deleteParticipant(int $id, ParticipantRepository $participantRepository, ManagerRegistry $doctrine, ResponsableRepository $responsableRepository, Connection $connection)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($id);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat delete', 'code' => 401]);

        $responsablesId = $connection->createQueryBuilder()
            ->select('r.id')
            ->from('responsable', 'r')
            ->where('participant_id = :id ')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAllAssociative();

        $entityManager = $doctrine->getManager();
        foreach ($responsablesId as $responsableId) {
            $responsable = $responsableRepository->find($responsableId);
            $responsableRepository->remove($responsable);
        }
        $participantRepository->remove($participant);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => "Esborrat correctament",
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/participants", methods={"POST"})
     */
    public function addParticipants(Request $request, ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $autoritzacio = $request->get('autoritzacio');
        $targetaSanitaria = $request->get('targetaSanitaria');
        $dataNaix = $request->get('dataNaixement');


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
        if ($autoritzacio == null) {
            return $response->setData([
                'success' => false,
                'data' => 'autoritzacio no indicat',
                'code' => 401
            ]);
        }
        if ($targetaSanitaria == null) {
            return $response->setData([
                'success' => false,
                'data' => 'targetaSanitaria no indicat',
                'code' => 401
            ]);
        }
        if ($dataNaix == null) {
            return $response->setData([
                'success' => false,
                'data' => 'dataNeixament no indicat',
                'code' => 401
            ]);
        }

        $participant = new Participant();
        $persona = new Persona();
        $persona->setNom($nom);
        $persona->setCognom1($cognom1);
        $persona->setCognom2($cognom2);
        $persona->setDNI($dni);
        $participant->setPersona($persona);
        $participant->setAutoritzacio($autoritzacio == "true");
        $participant->setTargetaSanitaria($targetaSanitaria);
        $time = new \DateTime($dataNaix);
        $participant->setDataNaixement($time);

        $entityManager = $doctrine->getManager();
        $participantRepository->add($participant);
        $entityManager->flush();

        $nom = $request->get('res1nom');
        $cognom1 = $request->get('res1cognom1');
        $cognom2 = $request->get('res1cognom2');
        $dni = $request->get('res1dni');
        $telefon1 = $request->get('res1telefon1');
        $telefon2 = $request->get('res1telefo2');
        $email = $request->get('res1email');
        $this->postResponsable($participant->getId(), $nom, $cognom1, $dni, $telefon1, $email,
            $responsableRepository, $participantRepository, $doctrine, $cognom2, $telefon2);

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
            ],
            'code' => 200
        ]);
    }

    private function postResponsable(int                   $idPart, string $nom, string $cognom1,
                                     string                $dni, int $telefon1,
                                     string                $email, ResponsableRepository $responsableRepository,
                                     ParticipantRepository $participantRepository, ManagerRegistry $doctrine,
                                     string                $cognom2 = null, int $telefon2 = null,)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($idPart);
        if ($participant == null) return $response->setData(['succes' => false, 'data' => "Participant no trobat", 'code' => 401]);

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
            'success' => true,
            'data' => [
                'id' => $responsable->getId(),
                'nom' => $responsable->getPersona()->getNom(),
                'cognom1' => $responsable->getPersona()->getCognom1(),
                'cognom2' => $responsable->getPersona()->getCognom2(),
                'dni' => $responsable->getPersona()->getDNI(),
                'email' => $responsable->getEmail(),
                'telefon1' => $responsable->getTelefon1(),
                'telefon2' => $responsable->getTelefon2(),
                'participantNom' => $responsable->getParticipant()->getPersona()->getNom()
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
        if ($cognom2 != null) {
            $participant->getPersona()->setCognom2($cognom2);
        }
        if ($dni != null) {
            $participant->getPersona()->setDNI($dni);
        }
        if ($autoritzacio != null) {
            $participant->setAutoritzacio($autoritzacio == "true");
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
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/participants/{idPart}/responsables/{idRes}",methods={"PUT"})
     */
    public function editResponsables(Request $request, int $idPart, int $idRes, ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $participant = $participantRepository->find($idPart);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Participant no trobat', 'code' => 401]);
        $responsable = $responsableRepository->find($idRes);
        if ($participant == null) return $response->setData(['success' => false, 'description' => 'Responsable no trobat', 'code' => 401]);
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $email = $request->get('email');
        $telefon1 = $request->get('telefon1');
        $telefon2 = $request->get('telefon2');
        if ($nom != null) {
            $responsable->getPersona()->setNom($nom);
        }
        if ($cognom1 != null) {
            $responsable->getPersona()->setCognom1($cognom1);
        }
        if ($cognom2 != null) {
            $responsable->getPersona()->setCognom2($cognom2);
        }
        if ($dni != null) {
            $responsable->getPersona()->setDNI($dni);
        }
        if ($email != null) {
            $responsable->setEmail($email);
        }
        if ($telefon1 != null) {
            $responsable->setTelefon1($telefon1);
        }
        if ($telefon2 != null) {
            $responsable->setTelefon2($telefon2);
        }
        $entityManager = $doctrine->getManager();
        $responsableRepository->add($responsable);
        $entityManager->flush();


        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $responsable->getId(),
                'nom' => $responsable->getPersona()->getNom(),
                'cognom1' => $responsable->getPersona()->getCognom1(),
                'cognom2' => $responsable->getPersona()->getCognom2(),
                'dni' => $responsable->getPersona()->getDNI(),
                'email' => $responsable->getEmail(),
                'telefon1' => $responsable->getTelefon1(),
                'telefon2' => $responsable->getTelefon2()
            ],
            'code' => 200
        ]);
    }


}