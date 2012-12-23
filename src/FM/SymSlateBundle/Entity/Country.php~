<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\CountryRepository")
 */
class Country
{
	/**
     * @ORM\OneToMany(targetEntity="CountryLanguage", mappedBy="country")
     */
     private $country_languages;
	 
	 
	 public function __construct()
	 {
	 	$this->country_languages = new \Doctrine\Common\Collections\ArrayCollection();
	 }
	
	public function __toString()
	{
		return $this->getName();
	}
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=16)
     */
    private $iso_code;

    /**
     * @var string
     *
     * @ORM\Column(name="ps_code", type="string", length=16)
     */
    private $ps_code;

    /**
     * @var string
     *
     * @ORM\Column(name="map_code", type="string", length=255)
     */
    private $map_code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set iso_code
     *
     * @param string $isoCode
     * @return Country
     */
    public function setIsoCode($isoCode)
    {
        $this->iso_code = $isoCode;
    
        return $this;
    }

    /**
     * Get iso_code
     *
     * @return string 
     */
    public function getIsoCode()
    {
        return $this->iso_code;
    }

    /**
     * Set ps_code
     *
     * @param string $psCode
     * @return Country
     */
    public function setPsCode($psCode)
    {
        $this->ps_code = $psCode;
    
        return $this;
    }

    /**
     * Get ps_code
     *
     * @return string 
     */
    public function getPsCode()
    {
        return $this->ps_code;
    }

    /**
     * Set map�_code
     *
     * @param string $map�Code
     * @return Country
     */
    public function setMap�Code($map�Code)
    {
        $this->map�_code = $map�Code;
    
        return $this;
    }

    /**
     * Get map�_code
     *
     * @return string 
     */
    public function getMap�Code()
    {
        return $this->map�_code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add country_languages
     *
     * @param \FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages
     * @return Country
     */
    public function addCountryLanguage(\FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages)
    {
        $this->country_languages[] = $countryLanguages;
    
        return $this;
    }

    /**
     * Remove country_languages
     *
     * @param \FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages
     */
    public function removeCountryLanguage(\FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages)
    {
        $this->country_languages->removeElement($countryLanguages);
    }

    /**
     * Get country_languages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountryLanguages()
    {
        return $this->country_languages;
    }

    /**
     * Set map_code
     *
     * @param string $mapCode
     * @return Country
     */
    public function setMapCode($mapCode)
    {
        $this->map_code = $mapCode;
    
        return $this;
    }

    /**
     * Get map_code
     *
     * @return string 
     */
    public function getMapCode()
    {
        return $this->map_code;
    }
}