<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface Extension
{
    /**
     * @param int $phpVersion
     * @return bool
     */
    public function supports(int $phpVersion): bool;
}
