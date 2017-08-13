<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface TokenExtension extends Extension
{
    /**
     * @param array $tokens
     * @return void
     */
	public function modifyYaccTokens(array &$tokens);

    /**
     * @param array $tokens
     * @return void
     */
	public function modifyLexerTokens(array &$tokens);
}
