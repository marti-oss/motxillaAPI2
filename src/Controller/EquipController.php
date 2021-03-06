<?php

namespace App\Controller;

use App\Entity\ActivitatProgramada;
use App\Entity\Equip;
use App\Repository\ActivitatProgramadaRepository;
use App\Repository\EquipRepository;
use App\Repository\MonitorRepository;
use App\Repository\ParticipantRepository;
use App\Repository\ResponsableRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EquipController extends AbstractController
{
    /**
     * @Route("/equips",methods={"GET"})
     */
    public function getEquips(Request $request, EquipRepository $repository)
    {
        //fet
        $response = new JsonResponse();

        $equipArray = [];
        $equips = $repository->findAll();
        foreach ($equips as $equip) {
            $equipArray[] = [
                'id' => $equip->getId(),
                'nom' => $equip->getNom(),
            ];
        }

        $response->setData([
            'success' => true,
            'data' => $equipArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/equips/{id}",name="equips_list",methods={"GET"})
     */
    public function getEquip(int $id, EquipRepository $repository)
    {
        //fet
        $response = new JsonResponse();
        $equipArray = [];
        $equip = $repository->find($id);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip' ,  'code' => 401]);

        $monitorsArray = [];
        foreach ($equip->getMonitors() as $monitor) {
            $monitorsArray[] = [
                'id' => $monitor->getId(),
                'nom' => $monitor->getPersona()->getNom(),
                'cognom1' => $monitor->getPersona()->getCognom1(),
                'cognon2' => $monitor->getPersona()->getCognom2(),
                'email' => $monitor->getUser()->getEmail(),
                'dni' => $monitor->getPersona()->getDNI(),
                'llicencia' => $monitor->getLlicencia(),
                'targetaSanitaria' => $monitor->getTargetaSanitaria()
            ];
        }
        $participantsArray = [];
        foreach ($equip->getParticipants() as $participant) {
            $participantsArray[] = [
                'id' => $participant->getId(),
                'nom' => $participant->getPersona()->getNom(),
                'cognom1' => $participant->getPersona()->getCognom1(),
                'cognon2' => $participant->getPersona()->getCognom2(),
                'autoritzacio' => $participant->isAutoritzacio(),
                'dni' => $participant->getPersona()->getDNI(),
                'dataNaixement' => $participant->getDataNaixement()->format("d/m/Y"),
                'targetaSanitaria' => $participant->getTargetaSanitaria()
            ];
        }
        $equipArray[] = [
            'id' => $equip->getId(),
            'nom' => $equip->getNom(),
            'monitors' => $monitorsArray,
            'participants' => $participantsArray
        ];

        $response->setData([
            'success' => true,
            'data' => $equipArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/equips/{id}/emails", methods={"POST"})
     */
    public function sendEmails(int $id, Request $request,EquipRepository $equipRepository, ParticipantRepository $participantRepository, ResponsableRepository $responsableRepository)
    {
        $response = new JsonResponse();
        $equipArray = [];
        $equip = $equipRepository->find($id);
        if ($equip == null) return $response->setData(['success' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);
        $participants = $participantRepository->findBy(array('Equip' => $equip->getId()));
        $reponsables = $responsableRepository->findBy(['Participant' => $participants]);

        $asumpte = $request->get('asumpte');
        $contingut = $request->get('contingut');
        foreach ($reponsables as $reponsable) {
            $email = (new Email())
                ->from('motxillatfg@gmail.com')
                ->to($reponsable->getEmail())
                ->subject($asumpte)
                ->text($contingut);
            $transport = new GmailSmtpTransport('motxillatfg', 'fmaytstkbbkizwum');
            $mailer = new Mailer($transport);
            $mailer->send($email);
        }

        $response->setData([
            'success' => true,
            'data' => $equipArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/equips/{id}",methods={"DELETE"})
     */
    public function deleteEquip(int $id, EquipRepository $repository, ManagerRegistry $doctrine, Connection $connection,
    ActivitatProgramadaRepository $activitatProgramadaRepository, ParticipantRepository $participantRepository)
    {
        $response = new JsonResponse();
        $equip = $repository->find($id);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);

        $entityManager = $doctrine->getManager();

        $activitatProgramadaIds = $connection->createQueryBuilder()
            ->select('acp.id')
            ->from('activitat_programada','acp')
            ->where('equip_id = :id')
            ->setParameter('id',$id)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($activitatProgramadaIds as $activitatProgramadaId){
            $activitatProgramada = $activitatProgramadaRepository->find($activitatProgramadaId);
            $activitatProgramadaRepository->remove($activitatProgramada);
        }

        $participantsId = $connection->createQueryBuilder()
            ->select('p.id')
            ->from('participant', 'p')
            ->where('equip_id = :id')
            ->setParameter('id',$id)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($participantsId as $participantId){
            $participant = $participantRepository->find($participantId);
            $participant->setEquip(null);
            $participantRepository->add($participant);
        }

        $repository->remove($equip);
        $entityManager->flush();

        $response->setData([
            'success' => true,
            'data' => "Esborrat correctament",
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("equips/{idEquip}/activitatsprogramades/{idActivitat}", methods={"DELETE"})
     */
    public function deleteActivitatProgramada(int                           $idEquip, int $idActivitat, EquipRepository $equipRepository,
                                              ActivitatProgramadaRepository $activitatProgramadaRepositoryRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $equip = $equipRepository->find($idEquip);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);

        $activitats = $equip->getActivitatsProgramades()->getValues();
        foreach ($activitats as $activitat) {
            if ($activitat->getId() == $idActivitat) {
                $entityManager = $doctrine->getManager();
                $activitatProgramadaRepositoryRepository->remove($activitat);
                $entityManager->flush();
                $response->setData([
                    'success' => true,
                    'data' => "Esborrat correctament",
                    'code' => 200
                ]);
                return $response;
            }
        }

        $response->setData([
            'success' => false,
            'data' => "No existeix activitat programada en aquest equip",
            'code' => 401
        ]);
        return $response;
    }

    /**
     * @Route("/equips",methods={"POST"})
     */
    public function addEquip(Request $request, EquipRepository $equipRepository, ManagerRegistry $doctrine,
    MonitorRepository $monitorRepository, ParticipantRepository $participantRepository)
    {
        $response = new JsonResponse();
        $equip = new Equip();
        $nom = $request->get("nom");
        if ($nom == null) {
            return $response->setData([
                'success' => false,
                'data' => "nom no indicat",
                'code' => 401
            ]);
        }

        $equip->setNom($nom);

        $monitorsList = $request->get("idmonitors");
        if(str_contains($monitorsList,"-")) {
            $monitorsIdArray = explode('-',$monitorsList);
            foreach ($monitorsIdArray as $idMonitor){
                $monitor = $monitorRepository->find($idMonitor);
                if($monitor == null )return $response->setData(['success' => false, 'data' => "monitor no trobat",  'code' => 401]);
                $equip->addMonitor($monitor);
            }
        }
        else {
            $monitor = $monitorRepository->find($monitorsList);
            if($monitor == null )return $response->setData(['success' => false, 'data' => $monitorsList,  'code' => 401]);
            $equip->addMonitor($monitor);
        }


        $participantsList = $request->get("idparticipants");

        if(str_contains($participantsList,"-")) {
            $participantsIdArray = explode('-',$participantsList);
            foreach ($participantsIdArray as $idParticipant){
                $participant = $participantRepository->find($idParticipant);
                if($participant == null )return $response->setData(['success' => false, 'data' => "participant amb id:". $idParticipant ."trobada",  'code' => 40000]);
                $equip->addParticipant($participant);
                $participant->setEquip($equip);
                $participantRepository->add($participant);
            }
        } else {
            $participant = $participantRepository->find($participantsList);
            if($participant == null )return $response->setData(['success' => false, 'data' => "monitor no trobada",  'code' => 401]);
            $participant->setEquip($equip);
            $participantRepository->add($participant);
        }

        $entityManager = $doctrine->getManager();
        $equipRepository->add($equip);
        $entityManager->flush();

        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $equip->getId(),
                'nom' => $equip->getNom()
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/equips/{id}",methods={"PUT"})
     */
    public function editEquip(int $id, Request $request, EquipRepository $equipRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();

        $equip = $equipRepository->find($id);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);
        $nom = $request->get('nom');
        if ($nom != null) {
            $equip->setNom($nom);
        }

        $entityManager = $doctrine->getManager();
        $equipRepository->add($equip);
        $entityManager->flush();

        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $equip->getId(),
                'nom' => $equip->getNom()
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/equips/{id}/activitatsprogramades",methods={"POST"})
     */
    public function addActivitatProgramada(int $id, Request $request, ManagerRegistry $doctrine, EquipRepository $equipRepository, ActivitatProgramadaRepository $activitatProgramadaRepository)
    {
        $response = new JsonResponse();
        $equip = $equipRepository->find($id);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);

        $dataIni = $request->get('dataIni');
        $dataFi = $request->get('dataFi');
        $nom = $request->get('nom');
        $objectiu = $request->get('objectiu');
        $interior = $request->get('interior');
        $descripcio = $request->get('descripcio');
        if ($dataIni == null) {
            return $response->setData([
                'success' => false,
                'data' => "dataIni no indicat",
                'code' => 401
            ]);
        }
        if ($dataFi == null) {
            return $response->setData([
                'success' => false,
                'data' => "dataFi no indicat",
                'code' => 401
            ]);
        }
        if ($nom == null) {
            return $response->setData([
                'success' => false,
                'data' => "nom no indicat",
                'code' => 401
            ]);
        }
        if ($objectiu == null) {
            return $response->setData([
                'success' => false,
                'data' => "objectiu no indicat",
                'code' => 401
            ]);
        }
        if ($interior == null) {
            return $response->setData([
                'success' => false,
                'data' => "interior no indicat",
                'code' => 401
            ]);
        }
        $timeIni = new \DateTime($dataIni);
        $timeFi = new \DateTime($dataFi);
        if ($timeIni > $timeFi) {
            return $response->setData([
                'success' => false,
                'data' => "dataIni no pot ser posterior a dataFi",
                'code' => 401
            ]);
        }

        $activitatprogramada = new ActivitatProgramada();
        $activitatprogramada->setNom($nom);
        $activitatprogramada->setDataIni($timeIni);
        $activitatprogramada->setDataFi($timeFi);
        $activitatprogramada->setDescripcio($descripcio);
        $activitatprogramada->setObjectiu($objectiu);
        $activitatprogramada->setInterior($interior);
        $activitatprogramada->setEquip($equip);

        $equip->addActivitatsProgramade($activitatprogramada);
        $entityManager = $doctrine->getManager();
        $activitatProgramadaRepository->add($activitatprogramada);
        $equipRepository->add($equip);
        $entityManager->flush();

        $act = [];
        $list = $equip->getActivitatsProgramades()->getValues();
        foreach ($list as $a) {
            $act[] = [
                'id' => $a->getId(),
                'nom' => $a->getNom(),
                'objectiu' => $a->getObjectiu(),
                'interior' => $a->isInterior(),
                'descripcio' => $a->getDescripcio(),
                'dataIni' => $a->getDataIni(),
                'dataFi' => $a->getDataFi(),
            ];
        }

        return $response->setData([
            'success' => true,
            'data' => [
                'idEquip' => $equip->getId(),
                'activitatprogramada' => $act
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/equips/{id}/activitatsprogramades",methods={"GET"})
     */
    public function getActivitatsProgramades(int $id, Request $request, EquipRepository $equipRepository,ActivitatProgramadaRepository $activitatProgramadaRepository ){
        $response = new JsonResponse();

        $equip = $equipRepository->find($id);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);

        $list = $equip->getActivitatsProgramades()->getValues();
        $act = [];
        foreach ($list as $a) {
            $act[] = [
                'id' => $a->getId(),
                'nom' => $a->getNom(),
                'objectiu' => $a->getObjectiu(),
                'interior' => $a->isInterior(),
                'descripcio' => $a->getDescripcio(),
                'dataIni' => $a->getDataIni(),
                'dataFi' => $a->getDataFi(),
            ];
        }

        return $response->setData([
            'success' => true,
            'data' => [
                'idEquip' => $equip->getId(),
                'activitatprogramada' => $act
            ],
            'code' => 200
        ]);
    }
    
    /**
     * @Route("/equips/{idEq}/monitors/{idMon}", methods={"DELETE"})
     */
    public function deleteMonitorDeLEquip(int $idEq, int $idMon, ManagerRegistry $doctrine, EquipRepository $equipRepository, MonitorRepository $monitorRepository){
        $response = new JsonResponse();
        $equip = $equipRepository->find($idEq);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);
        $monitor = $monitorRepository->find($idMon);
        if ($monitor == null) return $response->setData(['succes' => false, 'description' => 'No existeis monitor', 'code' => 401]);
        $equip->removeMonitor($monitor);

        $entityManager = $doctrine->getManager();
        $equipRepository->add($equip);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => "Esborrat monitor del equip correctament",
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/equips/{idEq}/participants/{idPar}", methods={"DELETE"})
     */
    public function deleteParticipantDeLEquip(int $idEq, int $idPar, ManagerRegistry $doctrine, EquipRepository $equipRepository, ParticipantRepository $participantRepository) {
        $response = new JsonResponse();
        $equip = $equipRepository->find($idEq);
        if ($equip == null) return $response->setData(['succes' => false, 'description' => 'No existeix l\'equip',  'code' => 401]);
        $participant = $participantRepository->find($idPar);
        if ($participant == null) return $response->setData(['succes' => false, 'description' => 'No existeis participant', 'code' => 401]);
        $equip->removeParticipant($participant);
        $participant->setEquip(null);
        $equipRepository->add($equip);
        $participantRepository->add($participant);
        $entityManager = $doctrine->getManager();
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => "Esborrat participant del equip correctament",
            'code' => 200
        ]);
        return $response;
    }

}