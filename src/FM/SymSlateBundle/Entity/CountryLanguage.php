<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CountryLanguage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\CountryLanguageRepository")
 */
class CountryLanguage
{
	 /**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="country_languages")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $country;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Language", inversedBy="country_languages")
	 * @ORM\JoinColumn(name="language_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $language;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer")
     */
    private $country_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language_id;

    /**
     * @var float
     *
     * @ORM\Column(name="percent", type="float")
     */
    private $percent;


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
     * Set country_id
     *
     * @param integer $countryId
     * @return CountryLanguage
     */
    public function setCountryId($countryId)
    {
        $this->country_id = $countryId;
    
        return $this;
    }

    /**
     * Get country_id
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Set language_id
     *
     * @param integer $languageId
     * @return CountryLanguage
     */
    public function setLanguageId($languageId)
    {
        $this->language_id = $languageId;
    
        return $this;
    }

    /**
     * Get language_id
     *
     * @return integer 
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * Set percent
     *
     * @param float $percent
     * @return CountryLanguage
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    
        return $this;
    }

    /**
     * Get percent
     *
     * @return float 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set country
     *
     * @param \FM\SymSlateBundle\Entity\Country $country
     * @return CountryLanguage
     */
    public function setCountry(\FM\SymSlateBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \FM\SymSlateBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return CountryLanguage
     */
    public function setLanguage(\FM\SymSlateBundle\Entity\Language $language = null)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return \FM\SymSlateBundle\Entity\Language 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}