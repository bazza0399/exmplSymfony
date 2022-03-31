<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employee;
use App\Form\EmployeeType;
// use App\Form\ProductFormType;
use App\Entity\Formation;


class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="app_formation")
     */
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    /**
    * @Route("/formations", name="formations")
    */
    public function formations()
    {
    $formations = $this->getDoctrine()->getRepository(Formation::class)->findDesc();

    return $this->render('formation/formations.html.twig', [
        "formations" => $formations,
    ]);
    }

    /**
    * @Route("/formations/{id}", name="details")
    */
    public function details($id, Request $request)
    {
    $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
    $employee = new Employee();
    $form = $this->createForm(EmployeeType::class, $employee);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($employee);
        $entityManager->flush();
        $new = $formation->getNbrParticipants() + 1;
        $formation->setNbrParticipants($new);
        $entityManager->persist($formation);
        $entityManager->flush();
    }

    return $this->render('formation/details.html.twig', [
        "formation" => $formation,
        "form" => $form->createView(),
    ]);
    }

    /**
     * @Route("/delete_formation/{id}", name="delete_formation")
     */
    public function deleteFormation(int $id): Response
    {
    $entityManager = $this->getDoctrine()->getManager();
    $formation = $entityManager->getRepository(Formation::class)->find($id);
    $entityManager->remove($formation);
    $entityManager->flush();

    return $this->redirectToRoute("formations");
}
}
