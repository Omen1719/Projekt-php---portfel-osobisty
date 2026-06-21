<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Entity\User;
use App\Service\TagServiceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms between a comma-separated string of tag titles and a list of Tag entities.
 *
 * @implements DataTransformerInterface<Collection<int, Tag>, string>
 */
class TagsDataTransformer implements DataTransformerInterface
{
    /**
     * Constructor.
     *
     * @param TagServiceInterface $tagService Tag service
     * @param Security            $security   Security
     */
    public function __construct(private readonly TagServiceInterface $tagService, private readonly Security $security)
    {
    }

    /**
     * Collection of Tag entities -> string shown in the form field.
     *
     * @param Collection<int, Tag>|null $value Tags collection
     *
     * @return string Comma-separated titles
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        $tagTitles = [];
        foreach ($value as $tag) {
            $tagTitles[] = $tag->getTitle();
        }

        return implode(', ', $tagTitles);
    }

    /**
     * String from the form field -> array of Tag entities (existing ones reused, new ones created).
     *
     * @param mixed $value Form value
     *
     * @return array<int, Tag>
     */
    public function reverseTransform($value): array
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return [];
        }

        $tagTitles = explode(',', (string) $value);

        $tags = [];
        foreach ($tagTitles as $tagTitle) {
            $title = trim($tagTitle);
            if ('' === $title) {
                continue;
            }

            $tag = $this->tagService->findOneByTitle($title, $user);
            if (null === $tag) {
                $tag = new Tag();
                $tag->setTitle($title);
                $tag->setAuthor($user);
            }

            $tags[] = $tag;
        }

        return $tags;
    }
}
