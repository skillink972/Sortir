<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[Groups(['group'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['group'])]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['group'])]
    #[ORM\Column(length: 255)]
    private ?string $rue = null;

    #[Groups(['group'])]
    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[Groups(['group'])]
    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[Groups(['group'])]
    #[ORM\ManyToOne(inversedBy: 'Lieux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville = null;

    #[Groups(['group'])]
    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Sortie::class)]
    private Collection $Sorties;

    public function __construct()
    {
        $this->Sorties = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->Sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->Sorties->contains($sorty)) {
            $this->Sorties->add($sorty);
            $sorty->setLieu($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->Sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getLieu() === $this) {
                $sorty->setLieu(null);
            }
        }
        return $this;
    }


}
