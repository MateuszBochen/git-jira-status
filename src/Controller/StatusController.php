<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * class StatusController
 * @package ${NAMESPACE}
 * @author Mateusz Bochen
 */
class StatusController extends AbstractController
{
    public function index()
    {
        return $this->render('Status/index.html.twig', [
            'number' => 20,
        ]);
    }
}
