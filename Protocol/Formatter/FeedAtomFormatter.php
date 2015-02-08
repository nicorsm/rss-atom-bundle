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

namespace Debril\RssAtomBundle\Protocol\Formatter;

use Debril\RssAtomBundle\Protocol\FeedFormatter;
use Debril\RssAtomBundle\Protocol\FeedOut;
use Debril\RssAtomBundle\Protocol\ItemOut;

/**
 * Class FeedAtomFormatter
 * @deprecated will be removed in version 2.0
 * @package Debril\RssAtomBundle\Protocol\Formatter
 */
class FeedAtomFormatter extends FeedFormatter
{

    const CONTENT_TYPE_HTML = 'html';

    /**
     *
     * @param  \Debril\RssAtomBundle\Protocol\FeedOut $content
     * @return string
     */
    public function toString(FeedOut $content)
    {
        $element = $this->toDom($content);

        return str_replace('default:', '', $element->saveXML());
    }

    /**
     *
     * @return \DomDocument
     */
    public function getRootElement()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $element = $dom->createElement('feed');
        $element->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $dom->appendChild($element);

        return $dom;
    }

    /**
     * @param \DomDocument                           $document
     * @param \Debril\RssAtomBundle\Protocol\FeedOut $content
     */
    public function setMetas(\DOMDocument $document, FeedOut $content)
    {
        $elements = array();
        $elements[] = $document->createElement('title', htmlspecialchars($content->getTitle()));
        $elements[] = $document->createElement('subtitle', $content->getDescription());
        $elements[] = $document->createElement('id', $content->getLink());

        $link = $document->createElement('link');
        $link->setAttribute('href', $content->getLink());
        $link->setAttribute('rel', 'self');

        $elements[] = $link;
        $elements[] = $document->createElement('updated', $content->getLastModified()->format(\DateTime::ATOM));

        foreach ($elements as $element) {
            $document->documentElement->appendChild($element);
        }
    }

    /**
     *
     * @param \DOMDocument                           $document
     * @param \Debril\RssAtomBundle\Protocol\ItemOut $item
     */
    protected function addEntry(\DOMDocument $document, ItemOut $item)
    {
        $entry = $document->createElement('entry');

        $elements = array();
        $elements[] = $document->createElement('title', htmlspecialchars($item->getTitle()));

        $link = $document->createElement('link');
        $link->setAttribute('href', $item->getLink());
        $elements[] = $link;

        $elements[] = $document->createElement('id', $item->getLink());
        $elements[] = $document->createElement('updated', $item->getUpdated()->format(\DateTime::ATOM));

        if (strlen($item->getSummary()) > 0) {
            $summary = $document->createElement('summary', htmlspecialchars($item->getSummary(), ENT_COMPAT, 'UTF-8'));
            $summary->setAttribute('type', self::CONTENT_TYPE_HTML);
        }

        $content = $document->createElement('content', htmlspecialchars($item->getDescription(), ENT_COMPAT, 'UTF-8'));
        $content->setAttribute('type', self::CONTENT_TYPE_HTML);
        $elements[] = $content;

        if (!is_null($item->getComment())) {
            $comments = $document->createElement('link');
            $comments->setAttribute('href', $item->getComment());
            $comments->setAttribute('rel', 'related');

            $elements[] = $comments;
        }

        if (!is_null($item->getAuthor())) {
            $author = $document->createElement('author');
            $author->appendChild($document->createElement('name', $item->getAuthor()));

            $elements[] = $author;
        }

        foreach ($elements as $element) {
            $entry->appendChild($element);
        }

        $document->documentElement->appendChild($entry);
    }

}
