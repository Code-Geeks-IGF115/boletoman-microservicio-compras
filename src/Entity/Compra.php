<?php

namespace App\Entity;

use App\Repository\CompraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompraRepository::class)]
class Compra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ver_compra'])]
    private ?int $id = null;

    #[Groups(['ver_compra'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $total = null;

    #[Groups(['ver_compra'])]
    #[ORM\OneToMany(mappedBy: 'compra', targetEntity: DetalleCompra::class, orphanRemoval: true)]
    private Collection $detalleCompras;

    public function __construct()
    {
        $this->detalleCompras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, DetalleCompra>
     */
    public function getDetalleCompras(): Collection
    {
        return $this->detalleCompras;
    }

    public function addDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if (!$this->detalleCompras->contains($detalleCompra)) {
            $this->detalleCompras->add($detalleCompra);
            $detalleCompra->setCompra($this);
        }

        return $this;
    }

    public function removeDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if ($this->detalleCompras->removeElement($detalleCompra)) {
            // set the owning side to null (unless already changed)
            if ($detalleCompra->getCompra() === $this) {
                $detalleCompra->setCompra(null);
            }
        }

        return $this;
    }
}
