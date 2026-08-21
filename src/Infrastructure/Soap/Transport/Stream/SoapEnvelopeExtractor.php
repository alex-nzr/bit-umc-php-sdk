<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream;

use ANZ\BitUmc\SDK\Domain\Exception\ResponseParseException;
use Throwable;

final class SoapEnvelopeExtractor
{
    public function extract($stream, int $maxBytes): SoapPayload
    {
        $payloadPath = tempnam(sys_get_temp_dir(), 'bit-umc-soap-');
        if ($payloadPath === false) {
            throw new ResponseParseException('Unable to create temporary SOAP payload storage.');
        }

        $payloadStream = fopen($payloadPath, 'wb');
        if ($payloadStream === false) {
            unlink($payloadPath);
            throw new ResponseParseException('Unable to open temporary SOAP payload storage.');
        }

        $state = new SoapEnvelopeExtractionState($payloadStream, $maxBytes);
        $parser = xml_parser_create_ns('UTF-8', '|');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($parser, [$state, 'startElement'], [$state, 'endElement']);
        xml_set_character_data_handler($parser, [$state, 'readText']);

        try {
            while (!feof($stream)) {
                $chunk = fread($stream, 8192);
                if ($chunk === false) {
                    throw new ResponseParseException('Unable to read SOAP response stream.');
                }

                if (!xml_parse($parser, $chunk, false)) {
                    throw new ResponseParseException('Unable to parse SOAP response envelope.');
                }
            }

            if (!xml_parse($parser, '', true)) {
                throw new ResponseParseException('Unable to parse SOAP response envelope.');
            }

            if ($state->hasFault()) {
                throw new ResponseParseException($state->getFaultMessage());
            }

            if (!$state->hasPayload()) {
                throw new ResponseParseException('SOAP response does not contain a return payload.');
            }
        } catch (Throwable $exception) {
            fclose($payloadStream);
            unlink($payloadPath);
            throw $exception;
        } finally {
            xml_parser_free($parser);
        }

        fclose($payloadStream);

        return new SoapPayload($payloadPath);
    }
}
