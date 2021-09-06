<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of dependency html.
 *
 * Dependency html is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency html is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency html. If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\html;

/**
 * Element plugins container
 *
 * @package Dependency\Html
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 **/
class PluginContainer extends \ArrayObject
{
    /**
     * The owner element of the container
     *
     * @var DOMElement
     */
    protected $element;

    /**
     * Constructs a new container
     * @param DOMElement $element
     */
    public function __construct($element)
    {
        $this->element = $element;
    }

    /**
     * Returns the requested plugin or instancites a new one
     *
     * @param string $name
     *
     * @return object
     */
    public function offsetGet($name)
    {
        if (!isset($this[$name]) && isset($this->element->ownerDocument->plugins[$name])) {
            $this->add($name);
        }

        return parent::offsetGet($name);
    }

    /**
     * Adds a plugin from a class
     *
     * @param string $name
     */
    public function add($name)
    {
        $pluginClass = $this->element->ownerDocument->plugins[$name];
        $plugin = new $pluginClass($this->element);
        $this[$name] = $plugin;
    }
}
