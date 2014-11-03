<?php

namespace dwpub\coffeeMakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoffeeBean
 */
class CoffeeBean
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $link;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return CoffeeBean
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }
}
