<?php

namespace App\Controller;

use App\Entity\Monitor;
use App\Entity\Persona;
use App\Entity\User;
use App\Repository\ActivitatProgramadaRepository;
use App\Repository\MonitorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class MonitorController extends AbstractController
{
    /**
     * @Route("/monitors/{id}/activitatsprogramades")
     */
    public function getActivitatsProgramadesMonitor(int $id, MonitorRepository $monitorRepository): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'El monitor no existeix',  'code' => 401]);
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
            'data' => $activitatsProgramadesArray,
            'code' => 200
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
        if ($monitor == null) return $response->setData(['success' => false, 'description' => "Monitor no existeix",  'code' => 401]);
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
            'data' => $activitatsProgramadesArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/",methods={"GET"})
     */
    public function getMonitors(MonitorRepository $monitorRepository): Response
    {
        $response = new JsonResponse();
        $monitors = $monitorRepository->findAll();
        $monitorsArray = [];
        foreach ($monitors as $monitor) {
            $monitorsArray[] = [
                'id' => $monitor->getId(),
                'nom' => $monitor->getPersona()->getNom(),
                'cognom1' => $monitor->getPersona()->getCognom1(),
                'cognom2' => $monitor->getPersona()->getCognom2(),
                'dni' => $monitor->getPersona()->getDNI(),
                'llicencia' => $monitor->getLlicencia(),
                'targetaSanitaria' => $monitor->getTargetaSanitaria(),
                'email' => $monitor->getUser()->getEmail()
            ];
        }
        $response->setData([
            'success' => true,
            'data' => $monitorsArray,
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/monitors/{id}",methods={"GET"})
     */
    public function getMonitor(MonitorRepository $monitorRepository, int $id, Security $security): Response
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor',  'code' => 401]);
        $monitorsArray = [
            'id' => $monitor->getId(),
            'nom' => $monitor->getPersona()->getNom(),
            'cognom1' => $monitor->getPersona()->getCognom1(),
            'cognom2' => $monitor->getPersona()->getCognom2(),
            'dni' => $monitor->getPersona()->getDNI(),
            'llicencia' => $monitor->getLlicencia(),
            'targetaSanitaria' => $monitor->getTargetaSanitaria(),
            'email' => $monitor->getUser()->getEmail(),
            'contrasenya' => $monitor->getUser()-> getPassword()
        ];
        $response->setData([
            'success' => true,
            'data' => $monitorsArray,
            'code' => 200
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
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor',  'code' => 401]);
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
            'data' => $equipArray,
            'code' => 200
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
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor',  'code' => 401]);
        $entityManager = $doctrine->getManager();
        $monitorRepository->remove($monitor);
        $entityManager->flush();
        $response->setData([
            'success' => true,
            'data' => 'Esborrat correctament',
            'code' => 200
        ]);
        return $response;
    }

    /**
     * @Route("/monitors",methods={"POST"})
     */
    public function addMonitor(Request $request, MonitorRepository $monitorRepository, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
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

       $monitor = new Monitor();
       $persona = new Persona();
       $user = new User();

       $persona->setNom($nom);
       $persona->setCognom1($cognom1);
       $persona->setCognom2($cognom2);
       $persona->setDNI($dni);

       $monitor->setPersona($persona);
       $monitor->setTargetaSanitaria($targetaSanitaria);
       $monitor->setLlicencia($llicencia);

       $user->setEmail($email);
       $plaintextPassword = uniqid();
       $hashedPassword = $passwordHasher->hashPassword(
           $user,
           $plaintextPassword
       );
       $user->setPassword($hashedPassword);
       $monitor->setUser($user);

        $entityManager = $doctrine->getManager();
        $monitorRepository->add($monitor);
        $entityManager->flush();

        $email = (new Email())
            ->from('martilluisiker@gmail.com')
            ->to($email)
            ->subject("Credencials de Motxilla")
            ->text("El teu email és: " . $email . "\n La teva contrasenya és: " .$plaintextPassword);
        $transport = new GmailSmtpTransport('motxillatfg', 'fmaytstkbbkizwum');
        $mailer = new Mailer($transport);
        $mailer->send($email);
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
                'email' => $monitor->getUser()->getEmail(),
                'contrasenya' => $monitor->getUser()-> getPassword()
            ],
            'code' => 200
        ]);
    }

    /**
     * @Route("/monitors/{id}",methods={"PUT"})
     */
    public function editMonitor(int $id, Request $request, MonitorRepository $monitorRepository, ManagerRegistry $doctrine)
    {
        $response = new JsonResponse();
        $monitor = $monitorRepository->find($id);
        if ($monitor == null) return $response->setData(['success' => false, 'description' => 'No existeix monitor',  'code' => 401]);
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
            $monitor->getUser()->setEmail($email);
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
                'email' => $monitor->getUser()->getEmail()
                //'contrasenya' => $monitor->getContrasenya()
            ],
            'code' => 200
        ]);
    }
}