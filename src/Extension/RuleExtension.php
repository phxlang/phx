<?php

namespace Phx\Extension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface RuleExtension extends Extension
{
    /**
     * @param array $ruleGroups
     * @return void
     */
	public function modifyYaccRules(array &$ruleGroups);
}
