<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classification
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="pack_id_message_id_idx", columns={"pack_id", "message_id"})})
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Entity
 */
class Classification
{
	
	/**
	 * @ORM\ManyToOne(targetEntity="MessagesImport", inversedBy="classifications")
	 * @ORM\JoinColumn(name="messages_import_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $messages_import;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Message", inversedBy="classifications")
	 * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $message;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Pack", inversedBy="classifications")
	 * @ORM\JoinColumn(name="pack_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $pack;
	 
	/**
     * @ORM\ManyToMany(targetEntity="Translation", inversedBy="classifications")
     * @ORM\JoinTable(name="CurrentTranslation")
     */
     private $translations;
	 
	 public function __construct()
	 {
	 	$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var integer
     *
     * @ORM\Column(name="messages_import_id", type="integer")
     */
    private $messages_import_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="pack_id", type="integer")
     */
    private $pack_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="message_id", type="integer")
     */
    private $message_id;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=64)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=64, nullable=true)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="subsection", type="string", length=64, nullable=true)
     */
    private $subsection;

	/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;


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
     * Set pack_id
     *
     * @param integer $packId
     * @return Classification
     */
    public function setPackId($packId)
    {
        $this->pack_id = $packId;
    
        return $this;
    }

    /**
     * Get pack_id
     *
     * @return integer 
     */
    public function getPackId()
    {
        return $this->pack_id;
    }

    /**
     * Set message_id
     *
     * @param integer $messageId
     * @return Classification
     */
    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
    
        return $this;
    }

    /**
     * Get message_id
     *
     * @return integer 
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Classification
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Classification
     */
    public function setSection($section)
    {
        $this->section = $section;
    
        return $this;
    }

    /**
     * Get section
     *
     * @return string 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set subsection
     *
     * @param string $subsection
     * @return Classification
     */
    public function setSubsection($subsection)
    {
        $this->subsection = $subsection;
    
        return $this;
    }

    /**
     * Get subsection
     *
     * @return string 
     */
    public function getSubsection()
    {
        return $this->subsection;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Classification
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Classification
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set messages_import
     *
     * @param \FM\SymSlateBundle\Entity\MessagesImport $messagesImport
     * @return Classification
     */
    public function setMessagesImport(\FM\SymSlateBundle\Entity\MessagesImport $messagesImport = null)
    {
        $this->messages_import = $messagesImport;
    
        return $this;
    }

    /**
     * Get messages_import
     *
     * @return \FM\SymSlateBundle\Entity\MessagesImport 
     */
    public function getMessagesImport()
    {
        return $this->messages_import;
    }

    /**
     * Set messages_import_id
     *
     * @param integer $messagesImportId
     * @return Classification
     */
    public function setMessagesImportId($messagesImportId)
    {
        $this->messages_import_id = $messagesImportId;
    
        return $this;
    }

    /**
     * Get messages_import_id
     *
     * @return integer 
     */
    public function getMessagesImportId()
    {
        return $this->messages_import_id;
    }

    /**
     * Set message
     *
     * @param \FM\SymSlateBundle\Entity\Message $message
     * @return Classification
     */
    public function setMessage(\FM\SymSlateBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return \FM\SymSlateBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set pack
     *
     * @param \FM\SymSlateBundle\Entity\Pack $pack
     * @return Classification
     */
    public function setPack(\FM\SymSlateBundle\Entity\Pack $pack = null)
    {
        $this->pack = $pack;
    
        return $this;
    }

    /**
     * Get pack
     *
     * @return \FM\SymSlateBundle\Entity\Pack 
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * Set translation
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translation
     * @return Classification
     */
    public function setTranslation(\FM\SymSlateBundle\Entity\Translation $translation = null)
    {
        $this->translation = $translation;
    
        return $this;
    }

    /**
     * Get translation
     *
     * @return \FM\SymSlateBundle\Entity\Translation 
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Add translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translations
     * @return Classification
     */
    public function addTranslation(\FM\SymSlateBundle\Entity\Translation $translations)
    {
        $this->translations[] = $translations;
    
        return $this;
    }

    /**
     * Remove translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translations
     */
    public function removeTranslation(\FM\SymSlateBundle\Entity\Translation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}