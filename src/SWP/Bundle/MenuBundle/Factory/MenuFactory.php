<?php

namespace SWP\Bundle\MenuBundle\Factory;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;

class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var \SplPriorityQueue
     */
    private $extensions;

    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
        $this->extensions = new \SplPriorityQueue();
        $this->addExtension(new CoreExtension(), -10);
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($name, array $options = array())
    {
        foreach ($this->getExtensions() as $extension) {
            $options = $extension->buildOptions($options);
        }

        $item = new $this->className($name, $this);

        foreach ($this->getExtensions() as $extension) {
            $extension->buildItem($item, $options);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension, $priority = 0)
    {
        $this->extensions->insert($extension, $priority);
    }

    /**
     * {@inheritdoc}
     */
    private function getExtensions()
    {
        return iterator_to_array($this->extensions, false);
    }
}
