<?php

namespace App\Controller;

use App\Entity\Monitor;
use App\Entity\Persona;
use App\Repository\ActivitatProgramadaRepository;
use App\Repository\MonitorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class MonitorController extends AbstractController
{
    /**
     * @Route("/monitors/{id}/activitatsprogramades")
     */
    public function getActivitatsProgramadesMonitor(int $id, MonitorRepository $monitorRepository): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'El monitor no existeix']);
        $equips = $monitor->getEquips();
        $activitatsProgramadesArray = [];
        foreach ($equips as $equip) {
            $activitats = $equip->getActivitatsProgramades();
            foreach ($activitats as $activitat) {
                $activitatsProgramadesArray[] = [
                    'id' => $activitat->getId(),
                    'nom' => $activitat->getNom(),
                    'objectiu' => $activitat->getObjectiu(),
                    'interior' => $activitat->isInterior(),
                    'descripcio' => $activitat->getDescripcio(),
                    'dataIni' => $activitat->getDataIni(),
                    'dataFi' => $activitat->getDataFi(),
                ];
            }
        }
        $response->setData([
            'success' => true,
            'data' => $activitatsProgramadesArray
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/{idMonitor}/activitatsprogramades/{idActivitat}")
     */
    public function getActivitatsProgramadaMonitor(int $idMonitor, MonitorRepository $monitorRepository, int $idActivitat, ActivitatProgramadaRepository $activitatProgramadaRepository): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($idMonitor);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => "Monitor no existeix"]);
        $equips = $monitor->getEquips();
        $activitatProgramada = $activitatProgramadaRepository->find($idActivitat);
        if ($activitatProgramada != null) {
            $activitatsProgramadesArray = [];
            foreach ($equips as $equip) {
                if ($equip->getActivitatsProgramades()->contains($activitatProgramada))
                    $activitatsProgramadesArray[] = [
                        'id' => $activitatProgramada->getId(),
                        'nom' => $activitatProgramada->getNom(),
                        'objectiu' => $activitatProgramada->getObjectiu(),
                        'interior' => $activitatProgramada->isInterior(),
                        'descripcio' => $activitatProgramada->getDescripcio(),
                        'dataIni' => $activitatProgramada->getDataIni(),
                        'dataFi' => $activitatProgramada->getDataFi(),
                    ];
            }
        }

        $response->setData([
            'success' => true,
            'data' => $activitatsProgramadesArray
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/")
     */
    public function getMonitors(MonitorRepository $monitorRepository): Response
    {
        $response = new JsonResponse();
        $monitors = $monitorRepository->findAll();
        $monitorsArray = [];
        foreach ($monitors as $monitor) {
            $monitorsArray = [
                'id' => $monitor->getId(),
                'nom' => $monitor->getPersona()->getNom(),
                'cognom1' => $monitor->getPersona()->getCognom1(),
                'cognom2' => $monitor->getPersona()->getCognom2(),
                'dni' => $monitor->getPersona()->getDNI(),
                'llicencia' => $monitor->getLlicencia(),
                'targetaSanitaria' => $monitor->getTargetaSanitaria(),
                'email' => $monitor->getEmail()
            ];
        }
        $response->setData([
            'success' => true,
            'data' => $monitorsArray
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/{id}",methods={"GET"})
     */
    public function getMonitor(MonitorRepository $monitorRepository, int $id): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor']);
        $monitorsArray = [
            'id' => $monitor->getId(),
            'nom' => $monitor->getPersona()->getNom(),
            'cognom1' => $monitor->getPersona()->getCognom1(),
            'cognom2' => $monitor->getPersona()->getCognom2(),
            'dni' => $monitor->getPersona()->getDNI(),
            'llicencia' => $monitor->getLlicencia(),
            'targetaSanitaria' => $monitor->getTargetaSanitaria(),
            'email' => $monitor->getEmail()
        ];
        $response->setData([
            'success' => true,
            'data' => $monitorsArray
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/{id}/equips")
     */
    public function getMonitorEquips(MonitorRepository $monitorRepository, int $id): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor']);
        $equips = $monitor->getEquips()->getValues();
        $equipArray = [];
        foreach ($equips as $equip) {
            $equipArray[] = [
                'id' => $equip->getId(),
                'name' => $equip->getNom(),
            ];
        }
        $response->setData([
            'success' => true,
            'data' => $equipArray
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/{id}",methods={"DELETE"})
     */
    public function deleteMonitor(MonitorRepository $monitorRepository, int $id, ManagerRegistry $doctrine): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor']);
        $entityManager = $doctrine->getManager();
        $monitorRepository->remove($monitor);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => 'Esborrat correctament'
        ]);
        return $response;
    }

    /**
     * @Route("/monitors",methods={"POST"})
     */
    public function addMonitor(Request $request, MonitorRepository $monitorRepository, ManagerRegistry $doctrine)
    {
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $llicencia = $request->get('llicencia');
        $targetaSanitaria = $request->get('targetaSanitaria');
        $email = $request->get('email');
        $response = new JsonResponse();
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
        if ($email == null) {
            return $response->setData([
                'success' => false,
                'data' => 'email no indicat'
            ]);
        }

        $monitor = new Monitor();
        $persona = new Persona();
        $persona->setNom($nom);
        $persona->setCognom1($cognom1);
        $persona->setCognom2($cognom2);
        $persona->setDNI($dni);
        $monitor->setPersona($persona);
        $monitor->setEmail($email);
        $monitor->setTargetaSanitaria($targetaSanitaria);
        $monitor->setLlicencia($llicencia);
        $monitor->setContrasenya(uniqid());
        $entityManager = $doctrine->getManager();
        $monitorRepository->add($monitor);
        $entityManager->flush();
        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $monitor->getId(),
                'nom' => $monitor->getPersona()->getNom(),
                'cognom1' => $monitor->getPersona()->getCognom1(),
                'cognom2' => $monitor->getPersona()->getCognom2(),
                'dni' => $monitor->getPersona()->getDNI(),
                'llicencia' => $monitor->getLlicencia(),
                'targetaSanitaria' => $monitor->getTargetaSanitaria(),
                'email' => $monitor->getEmail(),
                'contrasenya' => $monitor->getContrasenya()
            ]
        ]);
    }

    /**
     * @Route("/monitors/{id}",methods={"PUT"})
     */
    public function editMonitor(int $id, Request $request, MonitorRepository $monitorRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor']);
        $nom = $request->get('nom');
        $cognom1 = $request->get('cognom1');
        $cognom2 = $request->get('cognom2');
        $dni = $request->get('dni');
        $llicencia = $request->get('llicencia');
        $targetaSanitaria = $request->get('targetaSanitaria');
        $email = $request->get('email');
        if ($nom != null) {
            $monitor->getPersona()->setNom($nom);
        }
        if ($cognom1 != null) {
            $monitor->getPersona()->setCognom1($cognom1);
        }
        if ($cognom2 != null) {
            $monitor->getPersona()->setCognom2($cognom2);
        }
        if ($dni != null) {
            $monitor->getPersona()->setDNI($dni);
        }
        if ($llicencia != null) {
            if ($llicencia == "") $llicencia = null;
            $monitor->setLlicencia($llicencia);
        }
        if ($targetaSanitaria != null) {
            $monitor->setTargetaSanitaria($targetaSanitaria);
        }
        if ($email != null) {
            $monitor->setEmail($email);
        }
        $entityManager = $doctrine->getManager();
        $monitorRepository->add($monitor);
        $entityManager->flush();
        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $monitor->getId(),
                'nom' => $monitor->getPersona()->getNom(),
                'cognom1' => $monitor->getPersona()->getCognom1(),
                'cognom2' => $monitor->getPersona()->getCognom2(),
                'dni' => $monitor->getPersona()->getDNI(),
                'llicencia' => $monitor->getLlicencia(),
                'targetaSanitaria' => $monitor->getTargetaSanitaria(),
                'email' => $monitor->getEmail(),
                'contrasenya' => $monitor->getContrasenya()
            ]
        ]);
    }

    /**
     * @Route("/contrasenya", methods={"POST"})
     */
    public function canviarContrasenya(Request $request, MonitorRepository $monitorRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $cont_actual = $request->get("actual");
        $cont_nova = $request->get('nova');
        if ($cont_actual == null) return $response->setData(['success' => false, 'description' => 'actual no indicat']);
        if ($cont_nova == null) return $response->setData(['success' => false, 'description' => 'nova no indicat']);
        $user = $monitorRepository->find(3);
        $user->setContrasenya($cont_nova);
        $entityManager = $doctrine->getManager();
        $monitorRepository->add($user);
        $entityManager->flush();
        return $response->setData([
            'success' => true,
            'data' => [
                'id' => $user->getId(),
                'nom' => $user->getPersona()->getNom(),
                'cognom1' => $user->getPersona()->getCognom1(),
                'cognom2' => $user->getPersona()->getCognom2(),
                'dni' => $user->getPersona()->getDNI(),
                'llicencia' => $user->getLlicencia(),
                'targetaSanitaria' => $user->getTargetaSanitaria(),
                'email' => $user->getEmail(),
                'contrasenya' => $user->getContrasenya()
            ]
        ]);
    }
}