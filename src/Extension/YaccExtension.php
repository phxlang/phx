<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface YaccExtension extends Extension
{
	/**
	 * @return array
	 */
	public function extendYacc(): array;
}