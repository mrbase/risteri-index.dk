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

use CommerceGuys\Addressing\Model\Address as BaseAddress;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Address
 * @author  Ulrik Nielsen <me@ulrik.co>
 *
 * @MongoDB\EmbeddedDocument
 */
class Address extends BaseAddress
{
    /**
     * The locale.
     *
     * @MongoDB\String
     */
    protected $locale;

    /**
     * The two-letter country code.
     *
     * @MongoDB\String
     */
    protected $countryCode;

    /**
     * The top-level administrative subdivision of the country.
     *
     * @MongoDB\String
     */
    protected $administrativeArea;

    /**
     * The locality (i.e. city).
     *
     * @MongoDB\String
     */
    protected $locality;

    /**
     * The dependent locality (i.e. neighbourhood).
     *
     * @MongoDB\String
     */
    protected $dependentLocality;

    /**
     * The postal code.
     *
     * @MongoDB\String
     */
    protected $postalCode;

    /**
     * The sorting code.
     *
     * @MongoDB\String
     */
    protected $sortingCode;

    /**
     * The first line of the address block.
     *
     * @MongoDB\String
     */
    protected $addressLine1;

    /**
     * The second line of the address block.
     *
     * @MongoDB\String
     */
    protected $addressLine2;

    /**
     * The organization.
     *
     * @MongoDB\String
     */
    protected $organization;

    /**
     * The recipient.
     *
     * @MongoDB\String
     */
    protected $recipient;

    /**
     * @MongoDB\EmbedOne(targetDocument="Coordinates")
     */
    private $coordinates;

    /**
     * Set coordinates
     *
     * @param Coordinates $coordinates
     *
     * @return self
     */
    public function setCoordinates(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates
     *
     * @return Coordinates $coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getAddressLine1().', '.$this->getPostalCode().' '.$this->getLocality();
    }
}
