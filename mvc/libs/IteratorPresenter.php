<?php
namespace Libs;

/**
 *
 * @author bobthecow
 * @see https://gist.github.com/bobthecow/61161639d8be82a75b5e#file-iteratorpresenter-php
 *
 */
class IteratorPresenter implements \IteratorAggregate
{
    private $values;

    /**
     *
     * @param unknown $values
     * @throws InvalidArgumentException
     */
    public function __construct($values)
    {
        if (!is_array($values) && !$values instanceof Traversable) {
            throw new InvalidArgumentException('IteratorPresenter requires an array or Traversable object');
        }
        $this->values = $values;
    }

    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        $values = array();
        foreach ($this->values as $key => $val) {
            $values[$key] = array(
                'key'   => $key,
                'value' => $val,
                'first' => false,
                'last'  => false,
            );
        }
        $keys = array_keys($values);
        if (!empty($keys)) {
            $values[reset($keys)]['first'] = true;
            $values[end($keys)]['last']    = true;
        }
        return new \ArrayIterator($values);
    }
}