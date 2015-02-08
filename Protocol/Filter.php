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

namespace Debril\RssAtomBundle\Protocol;

/**
 * Interface Filter
 * @deprecated will be removed in version 2.0
 * @package Debril\RssAtomBundle\Protocol
 */
interface Filter
{

    /**
     * @param \Debril\RssAtomBundle\Protocol\Parser\Item
     * @return boolean
     */
    public function isValid(\Debril\RssAtomBundle\Protocol\Parser\Item $item);
}
