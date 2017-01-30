<?php

namespace Nico\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
* Owner
*
* @ORM\Table(name="owner")
* @ORM\Entity
*/
class Owner
{
    /**
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(name="creation_date", type="datetime")
    * @Assert\DateTime()
    */
    private $date;

    /**
    * @ORM\Column(name="owner_name", type="string")
    * @Assert\Type(type="string")
    */
    private $name;

    /**
    * @ORM\Column(name="owner_firstname", type="string")
    * @Assert\Type(type="string")
    */
    private $firstName;

    /**
    * @ORM\Column(name="email", type="string")
    * @Assert\Email()
    */
    private $email;

    /**
    * @ORM\Column(name="phone_number", type="integer")
    * @Assert\Type(type="integer")
    */
    private $phoneNumber;

    /**
    * @ORM\OneToMany(targetEntity="Nico\AppBundle\Entity\Circuit", mappedBy="owner", cascade={"persist"})
    */
    private $circuits;

    /**
     * @ORM\Column(name="token", type="string")
     */
    private $token;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->circuits = new ArrayCollection();
        $this->token = uniqid('', true);
    }

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
    * Set date
    *
    * @param \DateTime $date
    *
    * @return Owner
    */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
    * Get date
    *
    * @return \DateTime
    */
    public function getDate()
    {
        return $this->date;
    }

    /**
    * Set name
    *
    * @param string $name
    *
    * @return Owner
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
    * Set firstName
    *
    * @param string $firstName
    *
    * @return Owner
    */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
    * Get firstName
    *
    * @return string
    */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
    * Set email
    *
    * @param string $email
    *
    * @return Owner
    */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
    * Get email
    *
    * @return string
    */
    public function getEmail()
    {
        return $this->email;
    }

    /**
    * Set phoneNumber
    *
    * @param integer $phoneNumber
    *
    * @return Owner
    */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
    * Get phoneNumber
    *
    * @return integer
    */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
    * Add circuit
    *
    * @param \Nico\AppBundle\Entity\Circuit $circuit
    *
    * @return Owner
    */
    public function addCircuit(\Nico\AppBundle\Entity\Circuit $circuit)
    {
        $this->circuits[] = $circuit;

        return $this;
    }

    /**
    * Remove circuit
    *
    * @param \Nico\AppBundle\Entity\Circuit $circuit
    */
    public function removeCircuit(\Nico\AppBundle\Entity\Circuit $circuit)
    {
        $this->circuits->removeElement($circuit);
    }

    /**
    * Get circuits
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getCircuits()
    {
        return $this->circuits;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Owner
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
