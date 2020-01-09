<?php

declare(strict_types=1);

use Behatch\Context\JsonContext as BaseJsonContext;

final class JsonContext extends BaseJsonContext
{
    /**
     * Checks, that given JSON node is equal to the given string.
     *
     * @Then the JSON node :firstNode should be equal to :secondNode node
     */
    public function theJsonNodeShouldBeEqualToNode($firstNode, $secondNode)
    {
        $json = $this->getJson();

        $firstNodeValue = $this->inspector->evaluate($json, $firstNode);
        $secondNodeValue = $this->inspector->evaluate($json, $secondNode);

        if ($firstNodeValue !== $secondNodeValue) {
            throw new \Exception(sprintf('The node value are not equal `%s` !== `%s` ', \json_encode($firstNodeValue), \json_encode($secondNodeValue)));
        }
    }

    /**
     * @Then the JSON node :arg1 should not be equal to :arg2
     */
    public function theJsonNodeShouldNotBeEqualTo($arg1, $arg2)
    {
        $json = $this->getJson();

        $firstNodeValue = $this->inspector->evaluate($json, $arg1);

        if ($firstNodeValue === $arg2) {
            throw new \Exception(sprintf('%s and %s are identical!', $firstNodeValue, $arg2));
        }

        return $firstNodeValue;
    }
}
