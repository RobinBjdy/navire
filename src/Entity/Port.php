<?php

namespace App\Entity;

use App\Repository\PortRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @ORM\Entity(repositoryClass=PortRepository::class)
 * @ORM\Table(
 *      name="port" ,
 *      uniqueConstraints={@ORM\UniqueConstraint(name="portindicatif_unique",columns={"indicatif"})}
 * )
 */
class Port
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, name="nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=5, name="indicatif")
     * @Assert\Regex(
     *      pattern="/[A-Z]{5}/",
     *      message="L'indicatif du Port a strictement 5 caractères"
     * )
     */
    private $indicatif;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class)
     * @ORM\JoinColumn(nullable=false , name="idpays")
     */
    private $lePays;

    /**
     * @ORM\ManyToMany(targetEntity=AisShipType::class, inversedBy="lesPorts")
     * @ORM\JoinTable(
     *      name="porttypecompatible",
     *      joinColumns={@ORM\JoinColumn(name="idport", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idaistype", referencedColumnName="id")}
     * )
     * 
     */
    private $lesTypes;

    /**
     * @ORM\OneToMany(targetEntity=Escale::class, mappedBy="lePort", orphanRemoval=true)
     */
    private $lesEscales;

    /**
     * @ORM\OneToMany(targetEntity=Navire::class, mappedBy="portDestination")
     */
    private $naviresAttendus;

    public function __construct()
    {
        $this->lesTypes = new ArrayCollection();
        $this->lesEscales = new ArrayCollection();
        $this->portDestination = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIndicatif(): ?string
    {
        return $this->indicatif;
    }

    public function setIndicatif(string $indicatif): self
    {
        $this->indicatif = $indicatif;

        return $this;
    }

    public function getLePays(): ?Pays
    {
        return $this->lePays;
    }

    public function setLePays(?Pays $lePays): self
    {
        $this->lePays = $lePays;

        return $this;
    }

    /**
     * @return Collection|AisShipType[]
     */
    public function getLesTypes(): Collection
    {
        return $this->lesTypes;
    }

    public function addLesType(AisShipType $lesType): self
    {
        if (!$this->lesTypes->contains($lesType)) {
            $this->lesTypes[] = $lesType;
            $lesType->addLesPort($this);
        }

        return $this;
    }

    public function removeLesType(AisShipType $lesType): self
    {
        if ($this->lesTypes->removeElement($lesType)) {
            $lesType->removeLesPort($this);
        }

        return $this;
    }

    /**
     * @return Collection|Escale[]
     */
    public function getLesEscales(): Collection
    {
        return $this->lesEscales;
    }

    public function addLesEscale(Escale $lesEscale): self
    {
        if (!$this->lesEscales->contains($lesEscale)) {
            $this->lesEscales[] = $lesEscale;
            $lesEscale->setLePort($this);
        }

        return $this;
    }

    public function removeLesEscale(Escale $lesEscale): self
    {
        if ($this->lesEscales->removeElement($lesEscale)) {
            // set the owning side to null (unless already changed)
            if ($lesEscale->getLePort() === $this) {
                $lesEscale->setLePort(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Navire[]
     */
    public function getNaviresAttendus(): Collection
    {
        return $this->naviresAttendus;
    }

    public function addNaviresAttendus(Navire $portDestination): self
    {
        if (!$this->naviresAttendus->contains($portDestination)) {
            $this->naviresAttendus[] = $portDestination;
            $portDestination->setNaviresAttendus($this);
        }

        return $this;
    }

    public function removeNaviresAttendus(Navire $portDestination): self
    {
        if ($this->naviresAttendus->removeElement($portDestination)) {
            // set the owning side to null (unless already changed)
            if ($portDestination->getNaviresAttendus() === $this) {
                $portDestination->setNaviresAttendus(null);
            }
        }

        return $this;
    }
}
