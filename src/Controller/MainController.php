<?php

namespace App\Controller;

use App\Domain\Audit\AuditRepository;
use App\Domain\Common\Identifier\Reference;
use App\Domain\Porte\Enum\TypePorte;
use App\Domain\Porte\Table\UporteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/repository', name: 'app_repository')]
    public function repository(AuditRepository $repository): Response
    {
        $entity = $repository->find(Reference::create('2314E4477717N'));
        dump($entity);
        return $this->render('debug.html.twig');
    }

    #[Route('/table', name: 'app_table')]
    public function table(UporteRepository $repository): Response
    {
        $table = $repository->find_by(TypePorte::from(1));
        dump($table);
        return $this->render('debug.html.twig');
    }
}
