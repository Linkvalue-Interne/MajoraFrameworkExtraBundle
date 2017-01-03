<?php

namespace Majora\Framework\Tests\Form\Extension\Json\Functional;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Majora\Framework\Form\Extension\Json\JsonExtension;

class JsonExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var Form
     */
    private $formWithoutJson;

    public function setUp()
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new JsonExtension())
            ->getFormFactory();
        $this->form = $formFactory
            ->createBuilder(
                FormType::class,
                null,
                ['json_format' => true]
            )
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->getForm();
        $this->formWithoutJson = $formFactory
            ->createBuilder(FormType::class)
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->getForm();
    }

    public function testSubmitValidJsonShouldPopulateForm()
    {
        $this->form->submit('{ "name": "test1" }');
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getNormData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getViewData());
    }

    public function testSubmitInvalidJsonShouldThrowException()
    {
        $this->setExpectedExceptionRegExp(
            'InvalidArgumentException',
            '/^Invalid submitted json data, error (.*) : (.*), json : invalid json$/'
        );
        $this->form->submit('invalid json');
    }

    public function testFormWithoutJsonShouldWorkNormally()
    {
        $this->formWithoutJson->submit(['name' => 'test1']);
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->formWithoutJson->getData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->formWithoutJson->getNormData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->formWithoutJson->getViewData());
    }

    public function testSubmitWithoutStringShouldThrowException()
    {
        $this->setExpectedExceptionRegExp(
            'InvalidArgumentException',
            '/^Invalid argument: the submitted variable must be a string when you enable the json_format option.$/'
        );
        $this->form->submit(['invalid json']);
    }

    public function testRequestWithValidJsonShouldPopulateForm()
    {
        $request = $this->getRequest('{ "name": "test1" }');
        $this->form->handleRequest($request);
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getNormData());
        $this->assertEquals(['name' => 'test1', 'lastname' => null], $this->form->getViewData());
    }

    public function testRequestWithInvalidJsonShouldTHrowException()
    {
        $this->setExpectedExceptionRegExp(
            'InvalidArgumentException',
            '/^Invalid submitted json data, error (.*) : (.*), json : invalid json$/'
        );
        $request = $this->getRequest('invalid json');
        $this->form->handleRequest($request);
    }

    public function testRequestWithoutStringShouldThrowException()
    {
        $this->setExpectedExceptionRegExp(
            'InvalidArgumentException',
            '/^Invalid argument: the submitted variable must be a string when you enable the json_format option.$/'
        );
        $request = $this->getRequest(['test']);
        $this->form->handleRequest($request);
    }

    protected function getRequest($content)
    {
        return new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );
    }
}
