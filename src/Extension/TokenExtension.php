<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface TokenExtension extends Extension
{
	/**
	 * @return array
	 */
	public function extendTokens(): array;
}