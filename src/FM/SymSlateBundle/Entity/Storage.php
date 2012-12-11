<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storage
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="pack_id_message_id_idx", columns={"pack_id", "message_id"})})
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Entity
 */
class Storage
{
	
	/**
	 * @ORM\ManyToOne(targetEntity="MessagesImport", inversedBy="storages")
	 * @ORM\JoinColumn(name="messages_import_id", referencedColumnName="id")
	 */
	 private $messages_import;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Pack", inversedBy="storages")
	 * @ORM\JoinColumn(name="pack_id", referencedColumnName="id")
	 */
	 private $pack;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Message", inversedBy="storages")
	 * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
	 */
	 private $message;
	
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
     * @var integer
     *
     * @ORM\Column(name="messages_import_id", type="integer")
     */
    private $messages_import_id;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=64)
     */
    private $method;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=64)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="custom", type="string", length=64, nullable=true)
     */
    private $custom;


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
     * @return Storage
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
     * @return Storage
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
     * Set method
     *
     * @param string $method
     * @return Storage
     */
    public function setMethod($method)
    {
        $this->method = $method;
    
        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Storage
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Storage
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
     * Set custom
     *
     * @param string $custom
     * @return Storage
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;
    
        return $this;
    }

    /**
     * Get custom
     *
     * @return string 
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * Set messages_import_id
     *
     * @param integer $messagesImportId
     * @return Storage
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
     * @return Storage
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
     * Set pack
     *
     * @param \FM\SymSlateBundle\Entity\Pack $pack
     * @return Storage
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
     * Set message
     *
     * @param \FM\SymSlateBundle\Entity\Message $message
     * @return Storage
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
}