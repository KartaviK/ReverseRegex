<?php

namespace Kartavik\Kartigex\Generator;

/**
 * Interface AlternateInterface
 * @package Kartavik\Kartigex\Generator
 *
 * Allows a scope to select children using alternating strategy
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 * @since 0.0.1
 */
interface AlternateInterface
{
    /**
     * Tell the scope to select childing use alternating strategy
     */
    public function useAlternatingStrategy(): void;

    /**
     * Return true if setting been activated
     *
     * @return bool
     */
    public function usingAlternatingStrategy(): bool;
}
