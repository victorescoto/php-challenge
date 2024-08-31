<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

#[Entity, Table(name: 'stock_queries')]
final class StockQuery implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', length: 10, nullable: false)]
    private string $stockCode;

    #[Column(type: 'json', nullable: false)]
    private array $data;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'history')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(string $stockCode, array $data, User $user)
    {
        $this->stockCode = $stockCode;
        $this->data = $data;
        $this->user = $user;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStockCode(): string
    {
        return $this->stockCode;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'stockCode' => $this->stockCode,
            'data' => $this->data,
            'user' => $this->user->getId(),
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
