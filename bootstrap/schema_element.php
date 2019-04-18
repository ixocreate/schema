<?php
namespace Ixocreate\Schema\Package;

/** @var ElementConfigurator $element */
use Ixocreate\Schema\Package\Elements\DocumentElement;
use Ixocreate\Schema\Package\Elements\AudioElement;
use Ixocreate\Schema\Package\Elements\BlockContainerElement;
use Ixocreate\Schema\Package\Elements\CheckboxElement;
use Ixocreate\Schema\Package\Elements\CollectionElement;
use Ixocreate\Schema\Package\Elements\ColorElement;
use Ixocreate\Schema\Package\Elements\DateElement;
use Ixocreate\Schema\Package\Elements\DateTimeElement;
use Ixocreate\Schema\Package\Elements\GroupElement;
use Ixocreate\Schema\Package\Elements\HtmlElement;
use Ixocreate\Schema\Package\Elements\ImageElement;
use Ixocreate\Schema\Package\Elements\LinkElement;
use Ixocreate\Schema\Package\Elements\MapElement;
use Ixocreate\Schema\Package\Elements\MediaElement;
use Ixocreate\Schema\Package\Elements\MultiCheckboxElement;
use Ixocreate\Schema\Package\Elements\MultiSelectElement;
use Ixocreate\Schema\Package\Elements\NumberElement;
use Ixocreate\Schema\Package\Elements\PriceElement;
use Ixocreate\Schema\Package\Elements\RadioElement;
use Ixocreate\Schema\Package\Elements\SchemaElement;
use Ixocreate\Schema\Package\Elements\SectionElement;
use Ixocreate\Schema\Package\Elements\SelectElement;
use Ixocreate\Schema\Package\Elements\TabbedGroupElement;
use Ixocreate\Schema\Package\Elements\TextareaElement;
use Ixocreate\Schema\Package\Elements\TextElement;
use Ixocreate\Schema\Package\Elements\VideoElement;
use Ixocreate\Schema\Package\Elements\YouTubeElement;

$element->addElement(SchemaElement::class);
$element->addElement(TextElement::class);
$element->addElement(TextareaElement::class);
$element->addElement(SelectElement::class);
$element->addElement(MultiSelectElement::class);
$element->addElement(RadioElement::class);
$element->addElement(NumberElement::class);
$element->addElement(MultiCheckboxElement::class);
$element->addElement(ImageElement::class);
$element->addElement(DateTimeElement::class);
$element->addElement(DateElement::class);
$element->addElement(ColorElement::class);
$element->addElement(SectionElement::class);
$element->addElement(GroupElement::class);
$element->addElement(LinkElement::class);
$element->addElement(HtmlElement::class);
$element->addElement(YouTubeElement::class);
$element->addElement(PriceElement::class);
$element->addElement(MapElement::class);
$element->addElement(BlockContainerElement::class);
$element->addElement(TabbedGroupElement::class);
$element->addElement(CollectionElement::class);
$element->addElement(CheckboxElement::class);
$element->addElement(DocumentElement::class);
$element->addElement(MediaElement::class);
$element->addElement(DocumentElement::class);
$element->addElement(AudioElement::class);
$element->addElement(VideoElement::class);
