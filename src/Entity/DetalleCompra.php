<?php

namespace App\Entity;

use App\Repository\DetalleCompraRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: DetalleCompraRepository::class)]
class DetalleCompra
{
    #[Groups(['mis_eventos'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Groups(['ver_detallecompra','ver_compra'])]
    #[ORM\Column]
    private ?int $cantidad = null;

    #[Groups(['ver_detallecompra','ver_compra'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $total = null;

    #[Groups(['ver_detallecompra','ver_compra'])]
    #[ORM\Column(length: 100)]
    private ?string $descripcion = null;

    // #[Groups(['mis_eventos'])]
    #[ORM\ManyToOne(inversedBy: 'detalleCompras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compra $compra = null;

    public function __construct(array $parametrosarray)
    {
        $this->setDescripcion($parametrosarray['descripcion']);
        $this->setCantidad($parametrosarray['cantidad']);
        $this->setTotal($parametrosarray['total']);
        $this->setCompra($parametrosarray['compra']);
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
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

    public function getCompra(): ?Compra
    {
        return $this->compra;
    }

    public function setCompra(?Compra $compra): self
    {
        $this->compra = $compra;

        return $this;
    }
}
