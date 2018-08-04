<?php
/**
 * kiwi-suite/schema (https://github.com/kiwi-suite/schema)
 *
 * @package kiwi-suite/schema
 * @link https://github.com/kiwi-suite/schema
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\Schema\Elements;

use KiwiSuite\Cms\Block\BlockInterface;
use KiwiSuite\Cms\Block\BlockSubManager;
use KiwiSuite\CommonTypes\Entity\BlockContainerType;
use KiwiSuite\Contract\Schema\ElementInterface;
use KiwiSuite\Contract\Schema\SchemaInterface;
use KiwiSuite\Contract\Type\TypeInterface;
use KiwiSuite\Entity\Type\Type;
use KiwiSuite\Schema\Builder;

final class BlockContainerElement extends AbstractGroup
{
    private $blocks = [];
    /**
     * @var BlockSubManager
     */
    private $blockSubManager;

    /**
     * @var Builder
     */
    private $builder;

    public function __construct(BlockSubManager $blockSubManager, Builder $builder)
    {
        $this->blockSubManager = $blockSubManager;
        $this->builder = $builder;
    }

    public function type(): string
    {
        return BlockContainerType::class;
    }

    public function inputType(): string
    {
        return 'blockContainer';
    }

    public static function serviceName(): string
    {
        return 'blockContainer';
    }

    public function withBlocks(array $blocks): BlockContainerElement
    {
        $element = clone $this;
        $element->blocks = $this->parseBlockOption($blocks);

        foreach ($element->blocks as $block) {
            $element = $element->addBlockElement($block);
        }

        return $element;
    }

    public function blocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param ElementInterface $element
     * @throws \Exception
     * @return SchemaInterface
     */
    public function withAddedElement(ElementInterface $element): SchemaInterface
    {
        if (!($element instanceof GroupElement)) {
            throw new \Exception("Element must be a GroupElement");
        }
        return parent::withAddedElement($element);
    }

    public function jsonSerialize()
    {
        $array = parent::jsonSerialize();
        $array['blocks'] = $this->blocks();

        return $array;
    }

    public function transform($data): TypeInterface
    {
        return Type::create($data, BlockContainerType::class, ['schema' => $this, 'blocks' => $this->blocks()]);
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function parseBlockOption(array $blocks): array
    {
        //TODO duplicate code!!!
        $parsedBlocks = [];

        foreach ($blocks as $blockName) {
            if (\mb_strpos($blockName, '*') === false) {
                if (\array_key_exists($blockName, $this->blockSubManager->getServiceManagerConfig()->getNamedServices())) {
                    $parsedBlocks[] = $blockName;
                }
                continue;
            }

            $beginningPart = \mb_substr($blockName, 0, \mb_strpos($blockName, '*'));

            foreach (\array_keys($this->blockSubManager->getServiceManagerConfig()->getNamedServices()) as $mappingBlock) {
                if (\mb_substr($mappingBlock, 0, \mb_strlen($beginningPart)) === $beginningPart) {
                    $parsedBlocks[] = $mappingBlock;
                }
            }
        }

        return $parsedBlocks;
    }

    private function addBlockElement(string $block): ElementInterface
    {
        /** @var BlockInterface $blockObj */
        $blockObj = $this->blockSubManager->get($block);

        $schema = $blockObj->receiveSchema($this->builder);

        $group = $this->builder->create(GroupElement::class, $block);
        $group = $group->withLabel($blockObj->label());
        $group = $group->withElements($schema->elements());

        return $this->withAddedElement($group);
    }
}
