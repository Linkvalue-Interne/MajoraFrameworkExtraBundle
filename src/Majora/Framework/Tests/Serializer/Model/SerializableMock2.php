<?php

namespace Majora\Framework\Tests\Serializer\Model;

use Majora\Framework\Serializer\Model\SerializableInterface;
use Majora\Framework\Serializer\Model\SerializableTrait;

class SerializableMock2 implements SerializableInterface
{
    use SerializableTrait;

    protected $id = 2;

    protected $label = 'mock_2_label';

    protected $table = ['mock_2_1', 'mock_2_1'];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

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

    /**
     * @see ScopableInterface::getScopes()
     */
    public static function getScopes()
    {
        return [
            'default' => ['id', 'label', 'table'],
            'id' => 'id',
        ];
    }
}
