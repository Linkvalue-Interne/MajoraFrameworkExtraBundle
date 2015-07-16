<?php

namespace Majora\Bundle\FrameworkExtraBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Bundle\FrameworkExtraBundle\Controller\ControllerTrait;
use Majora\Framework\Serializer\Handler\Json\Exception\JsonDeserializationException;
use Majora\Framework\Validation\ValidationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base trait for REST APIs entity controllers traits.
 *
 * @property ContainerInterface $container
 */
trait RestApiControllerTrait
{
    use ControllerTrait;

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
     * @throws HttpException       if invalid json data
     * @throws ValidationException if invalid form
     */
    protected function assertSubmitedJsonFormIsValid(Request $request, FormInterface $form)
    {
        $data = @json_decode($request->getContent(), true);
        if (null === $data) {
            throw new HttpException(400, sprintf(
                'Invalid submitted json data, error %s : %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        // data camel case normalization
        $data = $this->container->has('majora.inflector') ?
            $this->container->get('majora.inflector')->normalize($data, 'camelize') :
            $data
        ;

        $form->submit(
            $data,
            $request->getMethod() !== 'PATCH'
        );

        if (!$valid = $form->isValid()) {
            throw new ValidationException(
                $form->getData(),
                $form->getErrors(true, true) // deep & flattened
            );
        }
    }

    /**
     * verify if intention on given resource (request if undefined) is granted
     *
     * @param string $intention
     * @param mixed  $resource
     *
     * @throws AccessDeniedHttpException if denied
     */
    protected function assertIsGrantedOr403($intention, $resource = null)
    {
        if (!$this->checkSecurity($intention, $resource)) {
            throw new AccessDeniedHttpException(sprintf(
                'Access denied while trying to "%s" an "%s" object.',
                $intention,
                is_object($resource) ? get_class($resource) : 'unknown'
            ));
        }
    }
}
