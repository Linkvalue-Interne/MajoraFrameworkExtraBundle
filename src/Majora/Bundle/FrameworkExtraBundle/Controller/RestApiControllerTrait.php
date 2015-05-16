<?php

namespace Majora\Bundle\FrameworkExtraBundle\Controller;

use Majora\Framework\Serializer\Handler\Json\Exception\JsonDeserializationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base class for REST APIs entity controllers traits.
 *
 * @property ContainerInterface $container
 */
trait RestApiControllerTrait
{
    /**
     * Extract available query filter from request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function extractQueryFilter(Request $request)
    {
        return array_map(
            function ($value) {
                return array_filter(explode(',', trim($value, ',')), function ($var) {
                    return !empty($var);
                });
            },
            $request->query->all()
        );
    }

    /**
     * Retrieves entity for given id into given repository service.
     *
     * @param $entityId
     * @param $loaderId
     *
     * @return Object
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function retrieveOr404($entityId, $loaderId)
    {
        if (!$this->container->has($loaderId)) {
            throw new NotFoundHttpException(sprintf('Unknow required loader : "%s"',
                $loaderId
            ));
        }

        if (!$entity = $this->container->get($loaderId)->retrieve($entityId)) {
            throw $this->createRest404($entityId, $loaderId);
        }

        return $entity;
    }

    /**
     * create a formatted http not found exception.
     *
     * @param string $entityId
     * @param string $loaderId
     *
     * @return NotFoundHttpException
     */
    protected function createRest404($entityId, $loaderId)
    {
        return new NotFoundHttpException(sprintf('Entity with id "%s" not found%s.',
            $entityId,
            $this->container->get('service_container')->getParameter('kernel.debug') ?
                sprintf(' : (looked into "%s")', $loaderId) :
                ''
        ));
    }

    /**
     * Create a JsonResponse with given data, if object given, it will be serialize
     * with registered serializer.
     *
     * @param mixed  $data
     * @param string $scope
     * @param int    $status
     *
     * @return Response
     */
    protected function createJsonResponse($data = null, $scope = null, $status = 200)
    {
        if ($data !== null) {
            $data = is_string($data) ?
                $data :
                $this->container->get('serializer')->serialize(
                    $data, 'json', empty($scope) ? array() : array('scope' => $scope)
                )
            ;
        }

        $response = new Response(null === $data ? '' : $data, $status);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * build and return a non content response.
     *
     * @return JsonResponse
     */
    protected function createJsonNoContentResponse()
    {
        $response = new Response(null, 204);
        $response->headers->set('Content-Type', null);

        return $response;
    }

    /**
     * create and returns a 400 Bad Request response.
     *
     * @param array $errors
     *
     * @return JsonResponse
     */
    protected function createJsonBadRequestResponse(array $errors = array())
    {
        // try to extract proper validation errors
        foreach ($errors as $key => $error) {
            if (!$error instanceof FormError) {
                continue;
            }
            $errors['errors'][$key] = array(
                'message'    => $error->getMessage(),
                'parameters' => $error->getMessageParameters(),
            );
            unset($errors[$key]);
        }

        $response = new Response(
            is_string($errors) ? $errors : json_encode($errors),
            400
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Custom method for form submission to handle http method bugs, and extra fields
     * error options.
     *
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return bool
     */
    protected function submitJsonData(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            throw new JsonDeserializationException(sprintf(
                'Invalid json data, error %s : %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        $form->submit($data);
        if (!$valid = $form->isValid()) {
            $this->container->get('logger')->notice(
                'Invalid form submitted',
                ['errors' => $form->getErrors(), 'data' => $data]
            );
        }

        return $valid;
    }

    /**
     * @see Symfony\Bundle\FrameworkBundle\Controller\Controller::createForm()
     */
    abstract public function createForm($type, $data = null, array $options = array());
}
