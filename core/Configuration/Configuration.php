<?php
/**
 * Class file for App Configuration
 * @package core\Configuration
 */
namespace core\Configuration;

/**
 * Class that defines the configuration singleton object
 */
class Configuration
    extends Section
{
    /* Constants */

    /* Properties */
    /**
     * The object storage for the singleton instance
     * @var array
     * @static
     * @access protected
     */
    protected static $instance;

    /**
     * The set of variables defined on configurations
     * @var array
     */
    public $variables;

    /* Methods */
    /**
     * Get the configuration instance. It can be
     *  * from configuration files
     *  * from Instance of application if loaded
     *  * from the already instantiated instance during the same call to app
     * @return \core\Configuration\Configuration object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            if ($configuration = \laabs::getCache('configuration')) {
                self::$instance = $configuration;
            } else {
                self::$instance = new Configuration();
                self::$instance->registerVariable('laabsDirectory', dirname(getcwd()));
                self::$instance->registerVariable('version', \laabs::getVersion());

                $confFile = \app::getConfiguration();
                self::$instance->loadFile($confFile);
                
                \app::setCache('configuration', self::$instance);
            }
        }

        return self::$instance;
    }

    /**
     * Registers a variable on the global scope
     * @param string $name  The name of the variable
     * @param mixed  $value The value of the variable
     * 
     * @return void
     */
    public function registerVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Retrieves a variable on the global scope
     * @param string $name the name of the variable
     * 
     * @return mixed $value The value of the variable
     */
    public function getVariable($name)
    {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }
    }

    /**
     * Parse and loads a configuration file into current section
     * @param string $conffile The path of a configuration file from conf root
     * 
     * @return void
     * 
     * @access protected
     */
    protected function loadFile($conffile)
    {   
        $parsedContent = $this->parseFile($conffile);

        if ($this->name) {
            $section = false;
        } else {
            $section = true;
        }

        $contents = parse_ini_string($parsedContent, $section);

        $this->load($contents);
    }

    protected function parseFile($conffile) 
    {
        if (!is_file($conffile)) {
            throw new \Exception("Unable to load configuration file '".$conffile."'");
        }

        $confdir = \laabs\dirname($conffile);

        // Load file or directory and process includes
        $confstring = "";
        $confhdl = fopen($conffile, 'r');
        while ($line = fgets($confhdl)) {
            if (strpos($line, "@include") === 0) {
                $includedFile = trim(str_replace("@include", "", $line));
                $includeDir = null;

                if (is_dir($includedFile)) {
                    $includeDir = $includedFile;
                } else if (is_dir($confdir . DIRECTORY_SEPARATOR . $includedFile)) {
                    $includeDir = $confdir . DIRECTORY_SEPARATOR . $includedFile;
                }
                else if (is_file($confdir . DIRECTORY_SEPARATOR . $includedFile)) {
                    $includedFile = $confdir . DIRECTORY_SEPARATOR . $includedFile;
                }

                if ($includeDir) {
                    foreach (glob($includeDir . DIRECTORY_SEPARATOR . "*.ini") as $filename) {
                        $includedFile = $filename;
                        $includedString = $this->parseFile($includedFile);
                        $confstring .= $includedString . PHP_EOL;
                    }
                } else {
                    $includedString = $this->parseFile($includedFile);
                    $confstring .= $includedString . PHP_EOL;
                }

            } else {
                $confstring .= str_replace('\"', '&quot;', $line);
            }
            
        }

        return $confstring;
    }


}