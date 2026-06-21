<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Operation entity — an income or expense registered in a wallet.
 */
#[ORM\Entity(repositoryClass: OperationRepository::class)]
#[ORM\Table(name: 'operations')]
class Operation
{
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    /** @var array<int, string> */
    public const TYPES = [self::TYPE_INCOME, self::TYPE_EXPENSE];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $amount = null;

    #[ORM\Column(type: 'string', length: 16)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::TYPES)]
    private ?string $type = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Type('string')]
    private ?string $note = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(Category::class)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Wallet::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(Wallet::class)]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'operations_tags')]
    private Collection $tags;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get amount.
     *
     * @return string|null Amount
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * Stored as a decimal string; accepts a number from the form (NumberType returns float).
     *
     * @param int|float|string|null $amount Amount
     */
    public function setAmount(int|float|string|null $amount): void
    {
        $this->amount = null === $amount ? null : (string) $amount;
    }

    /**
     * Get type.
     *
     * @return string|null Type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string|null $type Type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get note.
     *
     * @return string|null Note
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set note.
     *
     * @param string|null $note Note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * Get date.
     *
     * @return \DateTimeImmutable|null Date
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Set date.
     *
     * @param \DateTimeImmutable|null $date Date
     */
    public function setDate(?\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * Get created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set created at.
     *
     * @param \DateTimeImmutable|null $createdAt Created at
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updated at.
     *
     * @return \DateTimeImmutable|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Get wallet.
     *
     * @return Wallet|null Wallet
     */
    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    /**
     * Set wallet.
     *
     * @param Wallet|null $wallet Wallet
     */
    public function setWallet(?Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }

    /**
     * Get author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set author.
     *
     * @param User|null $author Author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag Tag
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    /**
     * Remove tag.
     *
     * @param Tag $tag Tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }
}
