<?php

namespace App\Entity;

use App\Repository\DescuentoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DescuentoRepository::class)]
class Descuento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $codigo = null;

    #[ORM\Column(length: 100)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $monto = null;

    #[ORM\Column]
    private ?int $cantidadButacas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

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

    public function getMonto(): ?string
    {
        return $this->monto;
    }

    public function setMonto(string $monto): self
    {
        $this->monto = $monto;

        return $this;
    }

    public function getCantidadButacas(): ?int
    {
        return $this->cantidadButacas;
    }

    public function setCantidadButacas(int $cantidadButacas): self
    {
        $this->cantidadButacas = $cantidadButacas;

        return $this;
    }
}
