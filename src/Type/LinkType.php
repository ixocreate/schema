<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Type;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Cms\Repository\PageRepository;
use Ixocreate\Cms\Router\PageRoute;
use Ixocreate\Media\Entity\Media;
use Ixocreate\Media\Repository\MediaRepository;
use Ixocreate\Media\Uri\MediaUri;
use Ixocreate\Schema\Builder\BuilderInterface;
use Ixocreate\Schema\Element\ElementInterface;
use Ixocreate\Schema\Element\ElementProviderInterface;
use Ixocreate\Schema\Element\LinkElement;

final class LinkType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var PageRoute
     */
    private $pageRoute;

    /**
     * @var MediaUri
     */
    private $uri;

    /**
     * LinkType constructor.
     *
     * @param PageRepository $pageRepository
     * @param MediaRepository $mediaRepository
     * @param PageRoute $pageRoute
     * @param MediaUri $uri
     */
    public function __construct(
        PageRepository $pageRepository,
        MediaRepository $mediaRepository,
        PageRoute $pageRoute,
        MediaUri $uri
    ) {
        $this->pageRepository = $pageRepository;
        $this->mediaRepository = $mediaRepository;
        $this->pageRoute = $pageRoute;
        $this->uri = $uri;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        if (!\is_array($value)) {
            return [];
        }

        if (empty($value['type'])) {
            return [];
        }

        switch ($value['type']) {
            case 'media':
                if (\is_array($value['value'])) {
                    if (empty($value['value']['id'])) {
                        return [];
                    }

                    $value['value'] = $value['value']['id'];
                }

                $value['value'] = $this->mediaRepository->find($value['value']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
            case 'sitemap':
                if (\is_array($value['value'])) {
                    if (empty($value['value']['id'])) {
                        return [];
                    }

                    $value['value'] = $value['value']['id'];
                }


                $value['value'] = $this->pageRepository->find($value['value']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
        }

        $target = "_self";
        if (\array_key_exists('target', $value) && \in_array($value['target'], ['_self', '_blank'])) {
            $target = $value['target'];
        }

        $value['target'] = $target;

        return [
            'type' => $value['type'],
            'target' => $value['target'],
            'value' => $value['value'],
        ];
    }

    /**
     * @return string|null
     */
    public function type(): ?string
    {
        $array = $this->value();

        if (empty($array)) {
            return null;
        }

        return $array['type'];
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type();
    }

    /**
     * @return string
     */
    public function target(): string
    {
        $array = $this->value();

        if (empty($array)) {
            return '_self';
        }

        return $array['target'];
    }

    /**
     * @return string
     * @deprecated
     */
    public function getTarget(): string
    {
        return $this->target();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $array = $this->value();

        if (empty($array)) {
            return "";
        }

        if (!empty($array['type'])) {
            switch ($array['type']) {
                case 'media':
                    return $this->assembleMediaUrl();
                case 'sitemap':
                    return $this->assemblePageUrl();
                case 'external':
                    return $this->assembleExternalUrl();
            }
        }


        return "";
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        $array = $this->value();

        if (empty($array)) {
            $array['value'] = null;
        } elseif ($array['type'] === "media" || $array['type'] === "sitemap") {
            $array['value'] = $array['value']->toPublicArray();
        }

        $array['link'] = null;
        if (!empty($array['type'])) {
            switch ($array['type']) {
                case 'media':
                    $array['link'] = $this->assembleMediaUrl();
                    break;
                case 'sitemap':
                    $array['link'] = $this->assemblePageUrl();
                    break;
                case 'external':
                    $array['link'] = $this->assembleExternalUrl();
                    break;
                default:
                    break;
            }
        }

        return $array;
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        $array = $this->value();

        if (empty($array) || empty($array['type'])) {
            /**
             * TODO: make links removable again
             */
            //$array['value'] = null; // like this?
            return null;
        }

        if ($array['type'] === "media" || $array['type'] === "sitemap") {
            $array['value'] = (string)$array['value']->id();
        }

        return $array;
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    /**
     * @return string
     */
    public static function serviceName(): string
    {
        return 'link';
    }

    /**
     * @return string
     */
    private function assemblePageUrl(): string
    {
        return $this->pageRoute->fromPage($this->value()['value']);
    }

    /**
     * @return string
     */
    private function assembleMediaUrl(): string
    {
        if (!($this->value()['value'] instanceof Media)) {
            return "";
        }

        return $this->uri->url($this->value()['value']);
    }

    /**
     * @return string
     */
    private function assembleExternalUrl(): string
    {
        return $this->value()['value'];
    }

    /**
     * @param BuilderInterface $builder
     * @return ElementInterface
     */
    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(LinkElement::class);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        /** @var LinkType $type */
        $type = Type::get(LinkType::serviceName());
        $this->mediaRepository = $type->mediaRepository;
        $this->pageRoute = $type->pageRoute;
        $this->pageRepository = $type->pageRepository;
        $this->uri = $type->uri;

        parent::unserialize($serialized);
    }
}
