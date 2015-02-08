<?php

/**
 * Rss/Atom Bundle for Symfony 2
 *
 * @package RssAtomBundle\Protocol
 *
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @copyright (c) 2013, Alexandre Debril
 *
 */

namespace Debril\RssAtomBundle\Protocol\Parser;

use Debril\RssAtomBundle\Protocol\FeedInterface;
use Debril\RssAtomBundle\Protocol\Parser;
use \SimpleXMLElement;

/**
 * Class RdfParser
 * @deprecated will be removed in version 2.0
 * @package Debril\RssAtomBundle\Protocol\Parser
 */
class RdfParser extends Parser
{

    protected $mandatoryFields = array(
        'channel',
    );

    /**
     *
     */
    public function __construct()
    {
        $this->setdateFormats(array(\DateTime::W3C, 'Y-m-d'));
    }

    /**
     * @param  SimpleXMLElement $xmlBody
     * @return boolean
     */
    public function canHandle(SimpleXMLElement $xmlBody)
    {
        return 'rdf' === strtolower($xmlBody->getName());
    }

    /**
     *
     * @param  SimpleXMLElement                             $xmlBody
     * @param  \Debril\RssAtomBundle\Protocol\FeedInterface $feed
     * @param  array                                        $filters
     * @return \Debril\RssAtomBundle\Protocol\FeedIn
     */
    protected function parseBody(SimpleXMLElement $xmlBody, FeedInterface $feed, array $filters)
    {
        $feed->setPublicId($xmlBody->channel->link);
        $feed->setLink($xmlBody->channel->link);
        $feed->setTitle($xmlBody->channel->title);
        $feed->setDescription($xmlBody->channel->description);

        if (isset($xmlBody->channel->date)) {
            $date = $xmlBody->channel->children('dc', true);
            $updated = self::convertToDateTime($date[0], $this->guessDateFormat($date[0]));
            $feed->setLastModified($updated);
        }

        $format = null;
        foreach ($xmlBody->item as $xmlElement) {
            $item = $this->newItem();
            $date = $xmlElement->children('dc', true);
            $format = !is_null($format) ? $format : $this->guessDateFormat($date[0]);

            $item->setXmlElement($xmlElement);
            
            $item->setTitle($xmlElement->title)
                    ->setDescription($xmlElement->description)
                    ->setUpdated(self::convertToDateTime($date[0], $format))
                    ->setLink($xmlElement->link);

            $this->addValidItem($feed, $item, $filters);
        }

        return $feed;
    }

}
