<?php

namespace Majora\Framework\Serializer\Tests\Model;

use Majora\Framework\Serializer\Model\SerializableInterface;
use Majora\Framework\Serializer\Model\SerializableTrait;

class SerializableMock1
    implements SerializableInterface
{
    use SerializableTrait;

    protected $id    = 1;
    protected $label = 'mock_1_label';
    protected $table = array('mock_1_1', 'mock_1_2');
    protected $protect;
    protected $mock2;
    protected $mock3;
    protected $callback;
    protected $date;

    public function __construct()
    {
        $this->mock2 = new SerializableMock2();
        $this->date  = new \DateTime('2015-01-01');
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setProtectedProtect($protect)
    {
        $this->protect = $protect;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable(array $table)
    {
        $this->table = $table;

        return $this;
    }

    public function getMock2()
    {
        return $this->mock2;
    }

    public function setMock2(SerializableMock2 $mock2)
    {
        $this->mock2 = $mock2;

        return $this;
    }

    public function getMock3()
    {
        return $this->mock3;
    }

    public function setMock3(\StdClass $mock3)
    {
        $this->mock3 = $mock3;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @see ScopableInterface::getScopes()
     */
    public static function getScopes()
    {
        return array(
            'default' => array('id', 'label'),
            'id'      => 'id',
            'full'    => array('@default', 'table', 'date', 'mock2@id'),
            'extra'   => array('@full', 'mock2'),
        );
    }
}
