<?php
/*
 * Copyright (C) 2018 Maarch
 *
 * This file is part of Laabs.
 *
 * Laabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Laabs.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace core;

/**
 * API discovery and documentation
 *
 * @package Laabs
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class Openapi
{
    public $swagger;
    public $info;
    public $host;
    public $basePath;
    public $schemes;
    public $consumes;
    public $produces;
    public $paths = [];
    public $definitions = [];
    //public $parameters = [];
    //public $responses = [];
    public $securityDefinitions = [];
    //public $security = [];

    /**
     * Run
     */
    public function __construct()
    {
        $this->swagger               = "2.0";
        $this->info();
        $this->host                  = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
        $this->basePath              = '/';
        $this->schemes               = ['http', 'https'];
        $this->consumes              = ['application/json'];
        $this->produces              = ['application/json'];
        
        // Get Paths + definition requests
        $this->paths();
        
        //$this->definitions = [];
        //$this->definitions();

        //$this->parameters = [];
        
        //$this->responses = [];
        
        $this->securityDefinitions();
        
        //$this->security              = $this->security();
        
        //$this->tags                  = $this->tags();
        
        $this->externalDocs();      
    }

    protected function info()
    {
        $this->info          = new \StdClass();
        $this->info->title   = 'Maarch RM';
        $this->info->version = \laabs::getVersion()[0]->number;
        $this->info->contact         = new \StdClass();
        //$this->info->contact->name   = 'Cyril VAZQUEZ';
        //$this->info->contact->email  = 'cyril.vazquez@maarch.org';
        $this->info->contact->url    = 'http://maarchrm.com';
        $this->info->license         = new \StdClass();
        $this->info->license->name   = 'GNU Lesser General Public License V3';
        $this->info->license->url    = 'http://www.gnu.org/licenses/lgpl.txt';
        $this->info->{'x-logo'}      = new \stdClass();
        $this->info->{'x-logo'}->url = 'presentation/img/RM.svg';
        $this->info->{'x-logo'}->altText = 'Maarch RM';
    }

    protected function paths()
    {
        if (isset($_GET['path'])) {
            $steps = explode('/', $_GET['path']);
            
            $bundle = array_shift($steps);
            $reflectionBundle = \laabs::bundle($bundle);
            
            if (empty($steps)) {
                $this->getBundlePaths($reflectionBundle);
            } else {
                $api = array_shift($steps);

                $reflectionApi = $reflectionBundle->getApi($api);

                $this->getApiPaths($reflectionApi);
            }
        } else {
            foreach (\laabs::bundles() as $reflectionBundle) {   
                $this->getBundlePaths($reflectionBundle);
            }
        }
    }

    protected function definitions()
    {
        if (empty($this->definitions)) {
            unset($this->definitions);

            return;
        }

        reset($this->definitions);
        while (current($this->definitions) == false && $typename = key($this->definitions)) {
            try {
                $definition = $this->getDefinition($typename);

                list ($bundle, $class) = explode('/', $typename);

                if (!isset($this->definitions[$bundle])) {
                    $this->definitions[$bundle] = new \StdClass();

                    $this->definitions[$bundle]->type = 'object';
                    $this->definitions[$bundle]->properties = [];
                }

                $this->definitions[$bundle]->properties[$class] = $definition;
            } catch (\exception $e) {

            }

            next($this->definitions);
        }

        foreach ($this->definitions as $typename => $definition) {
            if (!$definition) {
                try {
                    $definition = $this->getDefinition($typename);

                    list ($bundle, $class) = explode('/', $typename);

                    if (!isset($this->definitions[$bundle])) {
                        $this->definitions[$bundle] = new \StdClass();

                        $this->definitions[$bundle]->type = 'object';
                        $this->definitions[$bundle]->properties = [];
                    }

                    $this->definitions[$bundle]->properties[$class] = $definition;
                } catch (\exception $e) {

                }
            }
        }

        foreach ($this->definitions as $typename => $definition) {
            if (!$definition) {
                unset($this->definitions[$typename]);
            }
        }
    }

    protected function securityDefinitions()
    {
        $this->securityDefinitions['laabs']              = new \StdClass();
        $this->securityDefinitions['laabs']->type        = 'apiKey';
        $this->securityDefinitions['laabs']->description = "A service token provided by the administrator and send as a cookie named 'LAABS-AUTH'";
        $this->securityDefinitions['laabs']->name        = 'Cookie[LAABS-AUTH]';
        $this->securityDefinitions['laabs']->in          = 'header';
    }

    protected function security()
    {
        $security   = [];
        $security[] = $laabs = new \StdClass;
        $laabs->cookie = 'LAABS-AUTH';
    }

    protected function externalDocs()
    {
        $this->externalDocs              = new \StdClass();
        $this->externalDocs->description = 'Maarch online documentation';
        $this->externalDocs->url         = 'https://docs.maarch.org/gitbook/html/maarchRM/'.$this->info->version;
    }

    /* ************************************************************************
     *                                 Routines
     * ***********************************************************************/
    protected function getBundlePaths($reflectionBundle)
    {
        foreach ($reflectionBundle->getApis() as $reflectionApi) {
            $this->getApiPaths($reflectionApi);
        }
    }

    protected function getApiPaths($reflectionApi)
    {
        foreach ($reflectionApi->getPaths() as $reflectionPath) { 
            $method = $this->getMethod($reflectionPath);

            if (!$method || (isset($_GET['method']) && strtolower($_GET['method']) != $method)) {
                continue;
            }

            //$pathId = '/'.preg_replace('/\{[A-Za-z]+\}/', '{???}', $reflectionPath->path);
            $pathId = '/'.$reflectionPath->path;
            
            if (!isset($this->paths[$pathId])) {
                $this->paths[$pathId] = [];
            }

            $this->paths[$pathId][$method] = $this->getOperation($reflectionPath);
        }
    }

    protected function getOperation($reflectionPath)
    {
        $operation = new \StdClass();
        
        //$operation->tags = [];
        
        $this->getDescription($reflectionPath, $operation);

        //$operation->externalDocs = [];
        
        $operation->operationId = $reflectionPath->domain.'/'.$reflectionPath->interface.'/'.$reflectionPath->name;
        
        $operation->parameters = $this->getOperationParameters($reflectionPath);
       
        $operation->responses = $this->getOperationResponses($reflectionPath);
        
        if (empty($operation->responses)) {
            $operation->responses['default'] = $response = new \StdClass();
            $response->description = "No documentation available";
        }

        return $operation;
    }

    protected function getMethod($reflectionPath)
    {
        $method = strtolower($reflectionPath->method);
        switch ($method) {
            case 'create':
                return 'post';

            case 'read':
                return 'get';

            case 'update':
                return 'put';

            case 'delete' :
                return 'delete';
        }
    }

    protected function getOperationParameters($reflectionPath)
    {
        $parameters = [];

        $method = $this->getMethod($reflectionPath);

        if (!empty($reflectionPath->variables)) {
            $parameters = $this->getPathParameters($reflectionPath);
        }

        if (!empty($reflectionParameters = $reflectionPath->getParameters())) {
            switch ($method) {
                case 'get':
                case 'delete':
                    foreach ($reflectionParameters as $reflectionParameter) {
                        $parameters[] = $this->getParameter($reflectionParameter);
                    }
                    break;
            
                case 'post':
                case 'put':
                default:
                    $parameters[] = $this->getBodyDefinition($reflectionParameters);
            }
        }

        return $parameters;
    }

    protected function getPathParameters($reflectionPath)
    {
        $parameters = [];

        foreach ($reflectionPath->variables as $name => $def) {
            $parameter = new \StdClass();
            $parameter->name = $name;
            $parameter->in = 'path';
            $parameter->required = true;
            $parameter->type = 'string';

            $parameters[] = $parameter;
        }
        
        return $parameters;
    }

    protected function getParameter($reflectionParameter)
    {
        $parameter = new \StdClass();
        
        $parameter->name = $reflectionParameter->name;
        
        $parameter->in = 'query';

        $this->getDescription($reflectionParameter, $parameter);
        
        if (!$reflectionParameter->isDefaultValueAvailable() || !$reflectionParameter->allowsNull()) {
            $parameter->required = true;
        }

        $this->getSimpleType($reflectionParameter->type, $parameter);

        return $parameter;
    }

    protected function getBodyDefinition($reflectionParameters)
    {
        $parameter = new \StdClass();
        $parameter->name = 'payload';
        $parameter->in = 'body';
        $parameter->required = true;

        $parameter->schema = $schema = new \StdClass();
            
        $schema->type = 'object';
        
        foreach ($reflectionParameters as $reflectionParameter) {
            if (!$reflectionParameter->isOptional() && !$reflectionParameter->isDefaultValueAvailable()) {
                $schema->required[] = $reflectionParameter->name;
            }
            $schema->properties[$reflectionParameter->getName()] = $this->getBodyParameter($reflectionParameter);
        }
    
        return $parameter;
    }

    protected function getBodyParameter($reflectionParameter)
    {
        $property = new \StdClass();

        $this->getDescription($reflectionParameter, $property);
        
        if (!empty($reflectionParameter->type)) {
            $this->getDataType($reflectionParameter->type, $property);
        }

        return $property;
    }


    protected function getOperationResponses($reflectionPath)
    {
        $responses = [];

        try {
            if (isset($reflectionPath->action)) {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->action);
            } else {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->getName());
            }

            $reflectionAction = $actionRouter->action;

            if ($returnType = $reflectionAction->getReturnType()) {
                $response = new \StdClass();
                $this->getDescription($reflectionAction, $response);
                
                $response->schema = new \StdClass();
                $this->getDataType($returnType, $response->schema);

                if (count(get_object_vars($response->schema))) {
                    $responses['200'] = $response;
                }
            }

            if (!empty($thrownExceptions = $reflectionAction->getThrownExceptions())) {
                foreach ($thrownExceptions as $thrownException) {
                    if (strpos($thrownException, LAABS_URI_SEPARATOR) !== false) {
                        $bundleName = strtok($thrownException, LAABS_URI_SEPARATOR);
                        $exceptionName = strtok(LAABS_URI_SEPARATOR);
                        $reflectionException = \laabs::bundle($bundleName)->getException($exceptionName);
                        if ($code  = $reflectionException->getCode()) {
                            $response = new \StdClass();
                            $this->getDescription($reflectionException, $response);
                            
                            $response->schema = new \StdClass();
                            $response->schema->type = 'object';
                            $responses[$code] = $response;
                        }
                    } else {

                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $responses;
    }

    protected function getDataType($typename, $schema)
    {
        if (substr($typename, -2) == '[]') {
            $schema->type = 'array';
            $schema->items = new \StdClass();
            $this->getDataType(substr($typename, 0, -2), $schema->items);

            return;
        }

        if (strpos($typename, '/') !== false) {
            list ($bundle, $class) = explode('/', $typename);
            $schema->{'$ref'} = '#/definitions/'.$bundle.'/properties/'.$class;

            if (!isset($this->definitions[$bundle])) {
                $this->definitions[$bundle] = new \StdClass();

                $this->definitions[$bundle]->type = 'object';
                $this->definitions[$bundle]->properties = [];
            }

            if (!array_key_exists($class, $this->definitions[$bundle]->properties)) {
                $this->definitions[$bundle]->properties[$class] = null;

                $this->definitions[$bundle]->properties[$class] = $this->getDefinition($typename);
            }

            return;
        }

        $this->getSimpleType($typename, $schema);
    }

    protected function getSimpleType($typename, $schema)
    {
        switch ($typename) {
            case 'integer':
            case 'int':
                $schema->type = 'integer';
                break;

            case 'float':
                $schema->type = 'number';
                $schema->format = 'float';
                break;

            case 'double':
                $schema->type = 'number';
                $schema->format = 'double';
                break;

            case 'boolean':
            case 'bool':
                $schema->type = 'boolean';
                break;

            case 'date':
                $schema->type = 'string';
                $schema->format = 'date';
                break;

            case 'datetime':
            case 'timestamp':
                $schema->type = 'string';
                $schema->format = 'date-time';
                break;

            case 'id':
                $schema->type = 'string';
                $schema->pattern = '^[a-z_][a-z0-9\-_]*$';
                break;

            case 'qname':
                $schema->type = 'string';
                $schema->pattern = '^[a-z_][a-z0-9\-_]*\/[a-z_][a-z0-9\-_]*$';
                break;

            case 'duration':
                $schema->type = 'string';
                $schema->pattern = '^(\-\+)?P(\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?$';
                break;

            case 'array':
                $schema->type = 'array';
                $schema->items = new \StdClass();
                $schema->items->type = 'string';
                break;

            case 'string':
                $schema->type = 'string';
                break;

            case 'object':
                $schema->type = 'object';
                break;

            default:
                $schema->type = 'string';
        }
    }

    protected function getDefinition($typename)
    {
        $schema = new \StdClass();

        $schema->type = 'object';

        try {
            $reflectionType = \laabs::getMessage($typename);
        } catch (\Exception $e) {
            $reflectionType = \laabs::getClass($typename);
        }
        
        $this->getDescription($reflectionType, $schema);
    
        foreach ($reflectionType->getProperties() as $reflectionProperty) {
            if (!$reflectionProperty->isEmptyable() || !$reflectionProperty->isNullable()) {
                $schema->required[] = $reflectionProperty->name;
            }
            $schema->properties[$reflectionProperty->getName()] = $this->getTypeProperty($reflectionProperty);
        }

        return $schema;
    }

    protected function getTypeProperty($reflectionProperty)
    {
        $property = new \StdClass();
        $this->getDescription($reflectionProperty, $property);
        
        if (!empty($reflectionProperty->type)) {
            $this->getDataType($reflectionProperty->type, $property);
        }

        if (!empty($reflectionProperty->enumeration)) {
            $property->enum = $reflectionProperty->enumeration;
        }

        return $property;
    }

    protected function getDescription($reflection, $component)
    {
        $description ='';
        if (isset($reflection->summary)) {
            $description = trim($reflection->summary);
        }
        if (isset($reflection->description)) {
            $description .= $reflection->description;
        }

        if (!empty(trim($description))) {
            $component->description = trim($description);
        }

        $examples = $this->getExamples($reflection);
        if (!empty($examples)) {
            /*
            if (isset($component->description)) {
                $component->description .= PHP_EOL.PHP_EOL;
            }
            $component->description .= "### Examples".PHP_EOL;
            */
            $component->{'x-code-samples'} = [];
            
            foreach ($examples as $i => $example) {
                /*
                $component->description .= "[".$example->label."](".$_SERVER['REQUEST_SCHEME']."://".$this->host.$example->uri.')'.PHP_EOL; 
                $component->description .=
                    "```".$example->lang.PHP_EOL
                    .$example->source//file_get_contents('../web/'.$example->uri)
                    ."```".PHP_EOL;
                */

                $component->{'x-code-samples'}[] = $example;
            }
        }
    }

    protected function getExamples($reflection)
    {
        $examples = [];

        if (isset($reflection->tags['example'])) {
            foreach ($reflection->tags['example'] as $i => $exampleTag) {
                $example = new \stdClass();
                $example->uri = strtok($exampleTag, ' ');
                $example->label = strtok('');
                if (empty($example->label)) {
                    $example->label = 'Example '.($i+1);
                }
                $example->lang = 'json';
                if ($example->uri[0] == '/') {
                    $example->source = file_get_contents('../web'.$example->uri);
                } else {
                    $example->source = file_get_contents($example->uri);
                }

                $examples[] = $example;
            }
        }

        return $examples;
    }
}
