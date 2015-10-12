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
 * Class Coordinates
 * @author  Ulrik Nielsen <me@ulrik.co>
 *
 * @MongoDB\EmbeddedDocument
 */
class Coordinates
{
    /**
     * @MongoDB\Float
     */
    private $lat;

    /**
     * @MongoDB\Float
     */
    private $lon;

    /**
     * Set lat
     *
     * @param float $lat
     * @return self
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float $lat
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return self
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float $lon
     */
    public function getLon()
    {
        return $this->lon;
    }

    public function __toString()
    {
        return $this->lat.','.$this->lon;
    }
}
