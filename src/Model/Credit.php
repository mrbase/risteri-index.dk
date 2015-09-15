<?php
/**
 * This file is part of the risteri-index.dk package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Credit
 *
 * @package src\Model
 * @author  Ulrik Nielsen <me@ulrik.co>
 *
 * @MongoDB\EmbeddedDocument
 */
class Credit
{
    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $source;

    /**
     * @param $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
