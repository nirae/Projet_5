<?php

namespace Nico\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
* Image
*
* @ORM\Table(name="image")
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
*/
class Image
{
    /**
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(name="extension", type="string")
    */
    private $extension;

    /**
    * @ORM\Column(name="alt", type="string")
    */
    private $alt;

    /**
     * @Assert\File()
     */
    private $file;

    private $tempFilename;

    /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function preUpload()
    {
        // Si il n'y a pas de fichier, on sort directement
        if (null === $this->file) {
            return;
        }
        // Le nom du fichier est l'id, on doit juste stocker également son extension
        $this->extension = $this->file->guessExtension();
        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
    public function upload()
    {
        // Si il n'y a pas de fichier on sort directement
        if (null === $this->file) {
            return;
        }

        // Si on avait un ancien fichier, on le supprime
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $name = $this->id . '.' . $this->extension;
        // On déplace le fichier dans le dossier
        $this->file->move($this->getUploadRootDir(), $name);
    }

    /**
    * @ORM\PreRemove()
    */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
    * @ORM\PostRemove()
    */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename)) {
            // On supprime le fichier
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        return 'uploads/img';
    }

    public function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getFile()
    {
        return $this->file;
    }

    // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        // On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->extension) {
            // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->extension;

            // On réinitialise les valeurs des attributs extension et alt
            $this->extension = null;
            $this->alt = null;
        }
    }

    // Renvoi le lien complet
    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getId() . '.' . $this->getExtension();
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
    * Set extension
    *
    * @param string $extension
    *
    * @return Image
    */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
    * Get extension
    *
    * @return string
    */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
    * Set alt
    *
    * @param string $alt
    *
    * @return Image
    */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
    * Get alt
    *
    * @return string
    */
    public function getAlt()
    {
        return $this->alt;
    }
}
