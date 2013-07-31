<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TopContrib
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TopContrib
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="translated", type="integer")
     */
    private $translated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime")
     */
    private $date_updated;


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
     * Set translated
     *
     * @param integer $translated
     * @return TopContrib
     */
    public function setTranslated($translated)
    {
        $this->translated = $translated;
    
        return $this;
    }

    /**
     * Get translated
     *
     * @return integer 
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * Set date_updated
     *
     * @param \DateTime $dateUpdated
     * @return TopContrib
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->date_updated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get date_updated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }

    /**
     * Set user
     *
     * @param \FM\SymSlateBundle\Entity\User $user
     * @return TopContrib
     */
    public function setUser(\FM\SymSlateBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \FM\SymSlateBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}