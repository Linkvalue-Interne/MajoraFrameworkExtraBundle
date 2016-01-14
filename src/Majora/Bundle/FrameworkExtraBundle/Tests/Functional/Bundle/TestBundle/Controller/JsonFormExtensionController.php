<?php

namespace Majora\Bundle\FrameworkExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class JsonFormExtensionController extends Controller
{
    public function handleJsonAction(Request $request)
    {
        $formFactory = $this->container->get('form.factory');
        $form = $formFactory
            ->createBuilder(
                FormType::class,
                null,
                ['json_format' => true]
                )
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->getForm();

        try {
            $form->handleRequest($request);
        } catch (\Exception $e) {
            return new JsonResponse([
                'Class' => get_class($e),
                'Message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'Data' => $form->getData(),
            'NormData' => $form->getNormData(),
            'ViewData' => $form->getViewData(),
        ]);
    }

    public function handlePostAction(Request $request)
    {
        $formFactory = $this->container->get('form.factory');
        $form = $formFactory
            ->createBuilder(
                FormType::class
                )
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        return new JsonResponse([
            'Data' => $form->getData(),
            'NormData' => $form->getNormData(),
            'ViewData' => $form->getViewData(),
        ]);
    }
}
