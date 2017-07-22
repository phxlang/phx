<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface TokenExtension extends Extension
{

    const TYPE_TOKEN = 'token';

    const TYPE_LEFT = 'left';

    const TYPE_RIGHT = 'right';

    const TYPE_NONASSOC = 'nonassoc';

	/**
	 * @return array
	 */
	public function extendTokens(): array;
}