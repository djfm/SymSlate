<?php

namespace FM\Bundle\SlowShowBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\Bundle\SlowShowBundle\Entity\TaskRepository")
 */
class Task
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var string
     *
     * @ORM\Column(name="arguments", type="text", nullable = true)
     */
    private $arguments;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable = true)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="progress", type="float", nullable = true)
     */
    private $progress;

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
     * @var boolean
     *
     * @ORM\Column(name="started", type="boolean", nullable = false)
     */
	private $started;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="completed", type="boolean", nullable = false)
     */
	private $completed;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="failed", type="boolean", nullable = false)
     */
	private $failed;


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
     * Set user_id
     *
     * @param integer $userId
     * @return Task
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set arguments
     *
     * @param string $arguments
     * @return Task
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    
        return $this;
    }

    /**
     * Get arguments
     *
     * @return string 
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set progress
     *
     * @param float $progress
     * @return Task
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    
        return $this;
    }

    /**
     * Get progress
     *
     * @return float 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Task
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
     * @return Task
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
     * Set started
     *
     * @param boolean $started
     * @return Task
     */
    public function setStarted($started)
    {
        $this->started = $started;
    
        return $this;
    }

    /**
     * Get started
     *
     * @return boolean 
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set completed
     *
     * @param boolean $completed
     * @return Task
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    
        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean 
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set failed
     *
     * @param boolean $failed
     * @return Task
     */
    public function setFailed($failed)
    {
        $this->failed = $failed;
    
        return $this;
    }

    /**
     * Get failed
     *
     * @return boolean 
     */
    public function getFailed()
    {
        return $this->failed;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return Task
     */
    public function setService($service)
    {
        $this->service = $service;
    
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }
}