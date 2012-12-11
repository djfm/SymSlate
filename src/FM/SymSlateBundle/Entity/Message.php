<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Message
 * 
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="mkey_idx", columns={"mkey"})})
 * 
 */
class Message
{
	
	/**
	 * @ORM\ManyToOne(targetEntity="MessagesImport", inversedBy="messages", cascade={"persist"})
	 * @ORM\JoinColumn(name="messages_import_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $messages_import;
	
	/**
	 * @ORM\OneToMany(targetEntity="Classification", mappedBy="message")
	 */
	 private $classifications;
	 
	 /**
	 * @ORM\OneToMany(targetEntity="Storage", mappedBy="message")
	 */
	 private $storages;
	 
	 public function __construct()
	 {
	 	$this->classifications = new \Doctrine\Common\Collections\ArrayCollection();
		$this->storages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var string
     *
     * @ORM\Column(name="mkey", type="string", length=255)
     */
    private $mkey;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=32)
     */
    private $type;
	
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
     * Set mkey
     *
     * @param string $mkey
     * @return Message
     */
    public function setMkey($mkey)
    {
        $this->mkey = $mkey;
    
        return $this;
    }

    /**
     * Get mkey
     *
     * @return string 
     */
    public function getMkey()
    {
        return $this->mkey;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set messages_import_id
     *
     * @param integer $messagesImportId
     * @return Message
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
     * Set messages_import
     *
     * @param \FM\SymSlateBundle\Entity\MessagesImport $messagesImport
     * @return Message
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
     * Add classifications
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classifications
     * @return Message
     */
    public function addClassification(\FM\SymSlateBundle\Entity\Classification $classifications)
    {
        $this->classifications[] = $classifications;
    
        return $this;
    }

    /**
     * Remove classifications
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classifications
     */
    public function removeClassification(\FM\SymSlateBundle\Entity\Classification $classifications)
    {
        $this->classifications->removeElement($classifications);
    }

    /**
     * Get classifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassifications()
    {
        return $this->classifications;
    }

    /**
     * Add storages
     *
     * @param \FM\SymSlateBundle\Entity\Storage $storages
     * @return Message
     */
    public function addStorage(\FM\SymSlateBundle\Entity\Storage $storages)
    {
        $this->storages[] = $storages;
    
        return $this;
    }

    /**
     * Remove storages
     *
     * @param \FM\SymSlateBundle\Entity\Storage $storages
     */
    public function removeStorage(\FM\SymSlateBundle\Entity\Storage $storages)
    {
        $this->storages->removeElement($storages);
    }

    /**
     * Get storages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStorages()
    {
        return $this->storages;
    }
}