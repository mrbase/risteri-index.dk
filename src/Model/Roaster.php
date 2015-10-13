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

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Roaster
 *
 * @package src\Model
 * @author  Ulrik Nielsen <me@ulrik.co>
 *
 * @MongoDB\Document
 * @MongoDB\Index(keys={"address.coordinates"="2d"})
 * @MongoDB\HasLifecycleCallbacks
 */
class Roaster
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $slug;

    /**
     * @MongoDB\String
     */
    private $url;

    /**
     * @MongoDB\String
     */
    private $registrationNumber;

    /**
     * @MongoDB\EmbedMany(targetDocument="Credit")
     */
    private $credit;

    /**
     * @MongoDB\EmbedOne(targetDocument="Address")
     */
    private $address;

    /**
     * @MongoDB\Collection
     */
    private $tags;

    /**
     * @MongoDB\Hash
     */
    private $feeds;

    /**
     * @MongoDB\Distance
     */
    private $distance;

    /**
     * @MongoDB\Date
     */
    private $establishedAt;

    /**
     * @MongoDB\Date
     */
    private $invalidatedAt;

    /**
     * @MongoDB\Date
     */
    private $createdAt;

    /**
     * @MongoDB\Date
     */
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->credit = new ArrayCollection();
        $this->feeds  = [];
        $this->tags   = [];
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    public function setSlug()
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);

        return $this;
    }


    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug ?: false;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $reg
     *
     * @return self
     */
    public function setRegistrationNumber($reg)
    {
        $this->registrationNumber = $reg;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * @param ArrayCollection $credit
     *
     * @return $this
     */
    public function setCredit(ArrayCollection $credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param Credit $credit
     *
     * @return self
     */
    public function addCredit(Credit $credit)
    {
        foreach ($this->credit as $element) {
            if (json_encode($element->toArray()) === json_encode($credit->toArray())) {
                return $this;
            }
        }

        $this->credit->add($credit);

        return $this;
    }

    /**
     * @param Credit $credit
     *
     * @return self
     */
    public function removeCredit(Credit $credit)
    {
        $this->credit->removeElement($credit);

        return $this;
    }

    /**
     * Set address
     *
     * @param Address $address
     *
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get Address
     *
     * @return null|Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tag
     *
     * @return self
     */
    public function addTag($tag)
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param string $tag
     *
     * @return self
     */
    public function removeTag($tag)
    {
        $tags = array_reverse($this->tags, true);

        if (isset($tags[$tag])) {
            unset($this->tags[$tags[$tag]]);
        }

        return $this;
    }

    /**
     * @param array $feeds
     *
     * @return self
     */
    public function setFeeds(array $feeds)
    {
        $this->feeds = $feeds;

        return $this;
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getFeed($key)
    {
        if (isset($this->feeds[$key])) {
            return $this->feeds[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param string $feed
     *
     * @return self
     */
    public function addFeed($key, $feed)
    {
        if (empty($this->feeds[$key])) {
            $this->feeds[$key] = $feed;
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function removeFeed($key)
    {
        if (isset($this->feeds[$key])) {
            unset($this->feeds[$key]);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param \DateTime $date
     *
     * @return self
     */
    public function setEstablishedAt(\DateTime $date)
    {
        $this->establishedAt = $date;

        return $this;
    }

    /**
     * @param string $format Date format, if not set \DateTime object will be returned
     *
     * @return mixed
     */
    public function getEstablishedAt($format = null)
    {
        if (!is_null($format) && $this->establishedAt) {
            return $this->establishedAt->format($format);
        }

        return $this->establishedAt;
    }

    /**
     * @param \DateTime $date
     *
     * @return self
     */
    public function setInvalidatedAt(\DateTime $date)
    {
        $this->invalidatedAt = $date;

        return $this;
    }

    /**
     * @param string $format Date format, if not set \DateTime object will be returned
     *
     * @return mixed
     */
    public function getInvalidatedAt($format = null)
    {
        if (!is_null($format) && $this->invalidatedAt) {
            return $this->invalidatedAt->format($format);
        }

        return $this->invalidatedAt;
    }

    /**
     * Get createdAt
     *
     * @param string $format Date format, if not set \DateTime object will be returned
     *
     * @return string|\DateTime
     */
    public function getCreatedAt($format = null)
    {
        if (!is_null($format) && $this->createdAt) {
            return $this->createdAt->format($format);
        }

        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @param string $format Date format, if not set \DateTime object will be returned
     *
     * @return string|\DateTime
     */
    public function getUpdatedAt($format = null)
    {
        if (!is_null($format) && $this->updatedAt) {
            return $this->updatedAt->format($format);
        }

        return $this->updatedAt;
    }

    /**
     * Set createdAt and updatedAt in pre persist lifecycle.
     *
     * @MongoDB\PreUpdate
     * @MongoDB\PrePersist
     */
    public function prePersist()
    {
        $this->updatedAt = new \DateTime();

        // Only set createdAt on document creation.
        if (empty($this->createdAt)) {
            $this->createdAt = $this->updatedAt;
        }

        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);
    }
}
