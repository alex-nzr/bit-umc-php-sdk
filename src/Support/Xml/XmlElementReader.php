<?php

namespace ANZ\BitUmc\SDK\Support\Xml;

use ANZ\BitUmc\SDK\Domain\Exception\ResponseParseException;
use XMLReader;

final class XmlElementReader
{
    public function createReader(string $xml): XMLReader
    {
        $reader = new XMLReader();
        $flags = LIBXML_NONET | LIBXML_COMPACT | LIBXML_PARSEHUGE | LIBXML_NOERROR | LIBXML_NOWARNING;

        if (!$reader->XML($xml, null, $flags)) {
            throw new ResponseParseException('Unable to initialize XMLReader for SOAP response.');
        }

        return $reader;
    }

    public function readCurrentElementValue(XMLReader $reader): mixed
    {
        if ($reader->nodeType !== XMLReader::ELEMENT) {
            throw new ResponseParseException('XMLReader must point to an element node.');
        }

        $elementName = $reader->localName;
        if ($reader->isEmptyElement) {
            return '';
        }

        $children = [];
        $text = '';
        $depth = $reader->depth;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::END_ELEMENT && $reader->depth === $depth && $reader->localName === $elementName) {
                break;
            }

            if ($reader->nodeType === XMLReader::ELEMENT) {
                $this->appendChild($children, $reader->localName, $this->readCurrentElementValue($reader));
                continue;
            }

            if ($reader->nodeType === XMLReader::TEXT || $reader->nodeType === XMLReader::CDATA || $reader->nodeType === XMLReader::SIGNIFICANT_WHITESPACE) {
                $text .= $reader->value;
            }
        }

        if ($children === []) {
            return trim($text);
        }

        $trimmedText = trim($text);
        if ($trimmedText !== '') {
            $children['_text'] = $trimmedText;
        }

        return $children;
    }

    private function appendChild(array &$children, string $name, mixed $value): void
    {
        if (!array_key_exists($name, $children)) {
            $children[$name] = $value;
            return;
        }

        if (!is_array($children[$name]) || !array_is_list($children[$name])) {
            $children[$name] = [$children[$name]];
        }

        $children[$name][] = $value;
    }
}
