<?php
/**
 * $Id: CoverageThresholdTask.php 727 2010-02-13 13:36:27Z bschultz $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once 'phing/Task.php';
require_once 'phing/system/io/PhingFile.php';
require_once 'phing/system/util/Properties.php';

/**
 * Stops the build if any of the specified coverage threshold was not reached
 *
 * @author  Benjamin Schultz <bschultz@proqrent.de>
 * @version $Id: CoverageThresholdTask.php 727 2010-02-13 13:36:27Z bschultz $
 * @package phing.tasks.ext.coverage
 */
class CoverageThresholdTask extends Task
{
    /**
     * Holds an optional classpath
     *
     * @var Path
     */
    private $_classpath = null;

    /**
     * Holds an optional database file
     *
     * @var PhingFile
     */
    private $_database = null;

    /**
     * Holds the coverage threshold for the entire project
     *
     * @var integer
     */
    private $_perProject = 25;

    /**
     * Holds the coverage threshold for any class
     *
     * @var integer
     */
    private $_perClass = 25;

    /**
     * Holds the coverage threshold for any method
     *
     * @var integer
     */
    private $_perMethod = 25;

    /**
     * Number of statements in the entire project
     *
     * @var integer
     */
    private $_projectStatementCount = 0;

    /**
     * Number of covered statements in the entire project
     *
     * @var integer
     */
    private $_projectStatementsCovered = 0;

    /**
     * Sets an optional classpath
     *
     * @param Path $classpath The classpath
     */
    public function setClasspath(Path $classpath)
    {
        if ($this->_classpath === null) {
            $this->_classpath = $classpath;
        } else {
            $this->_classpath->append($classpath);
        }
    }

    /**
     * Sets the optional coverage database to use
     *
     * @param PhingFile The database file
     */
    public function setDatabase(PhingFile $database)
    {
        $this->_database = $database;
    }

    /**
     * Create classpath object
     *
     * @return Path
     */
    public function createClasspath()
    {
        $this->classpath = new Path();
        return $this->classpath;
    }

    /**
     * Sets the coverage threshold for entire project
     *
     * @param string $threshold Coverage threshold for entire project
     */
    public function setPerProject($threshold)
    {
        $this->_perProject = $threshold;
    }

    /**
     * Sets the coverage threshold for any class
     *
     * @param string $threshold Coverage threshold for any class
     */
    public function setPerClass($threshold)
    {
        $this->_perClass = $threshold;
    }

    /**
     * Sets the coverage threshold for any method
     *
     * @param string $threshold Coverage threshold for any method
     */
    public function setPerMethod($threshold)
    {
        $this->_perMethod = $threshold;
    }

    /**
     * Filter covered statements
     *
     * @param integer $var Coverage CODE/count
     * @return boolean
     */
    protected function filterCovered($var)
    {
        return ($var >= 0 || $var === -2);
    }

    /**
     * Calculates the coverage threshold
     *
     * @param string $filename            The filename to analyse
     * @param array  $coverageInformation Array with coverage information
     */
    protected function calculateCoverageThreshold($filename, $coverageInformation)
    {
        $classes = PHPUnitUtil::getDefinedClasses($filename, $this->_classpath);

        if (is_array($classes)) {
            foreach ($classes as $className) {
                $reflection     = new ReflectionClass($className);
                $classStartLine = $reflection->getStartLine();

                // Strange PHP5 reflection bug, classes without parent class
                // or implemented interfaces seem to start one line off
                if (   $reflection->getParentClass() === null
                    && count($reflection->getInterfaces()) === 0) {
                    unset($coverageInformation[$classStartLine + 1]);
                } else {
                    unset($coverageInformation[$classStartLine]);
                }

                reset($coverageInformation);

                $methods = $reflection->getMethods();

                foreach ($methods as $method) {
                    // PHP5 reflection considers methods of a parent class
                    // to be part of a subclass, we don't
                    if ($method->getDeclaringClass()->getName() != $reflection->getName()) {
                        continue;
                    }

                    $methodStartLine = $method->getStartLine();
                    $methodEndLine   = $method->getEndLine();

                    // small fix for XDEBUG_CC_UNUSED
                    if (isset($coverageInformation[$methodStartLine])) {
                        unset($coverageInformation[$methodStartLine]);
                    }

                    if (isset($coverageInformation[$methodEndLine])) {
                        unset($coverageInformation[$methodEndLine]);
                    }

                    if ($method->isAbstract()) {
                        continue;
                    }

                    $lineNr = key($coverageInformation);

                    while ($lineNr !== null && $lineNr < $methodStartLine) {
                        next($coverageInformation);
                        $lineNr = key($coverageInformation);
                    }

                    $methodStatementsCovered = 0;
                    $methodStatementCount    = 0;

                    while ($lineNr !== null && $lineNr <= $methodEndLine) {
                        $methodStatementCount++;

                        $lineCoverageInfo = $coverageInformation[$lineNr];
                        // set covered when CODE is other than -1 (not executed)
                        if ($lineCoverageInfo > 0 || $lineCoverageInfo === -2) {
                            $methodStatementsCovered++;
                        }

                        next($coverageInformation);
                        $lineNr = key($coverageInformation);
                    }

                    if ($methodStatementCount > 0) {
                        $methodCoverage = (  $methodStatementsCovered
                                           / $methodStatementCount) * 100;
                    } else {
                        $methodCoverage = 0;
                    }

                    if ($methodCoverage < $this->_perMethod) {
                        throw new BuildException(
                            'The coverage (' . $methodCoverage . '%) '
                            . 'for method "' . $method->getName() . '" is lower'
                            . ' than the specified threshold ('
                            . $this->_perMethod . '%)'
                        );
                    }
                }

                $classStatementCount    = count($coverageInformation);
                $classStatementsCovered = count(
                    array_filter(
                        $coverageInformation,
                        array($this, 'filterCovered')
                    )
                );

                if ($classStatementCount > 0) {
                    $coverage = (  $classStatementsCovered
                                 / $classStatementCount) * 100;
                } else {
                    $coverage = 0;
                }

                if ($coverage < $this->_perClass) {
                    throw new BuildException(
                        'The coverage (' . $coverage . '%) for class "'
                        . $reflection->getName() . '" is lower than the '
                        . 'specified threshold (' . $this->_perClass . '%)'
                    );
                }

                $this->_projectStatementCount    += $classStatementCount;
                $this->_projectStatementsCovered += $classStatementsCovered;
            }
        }
    }

    public function main()
    {
        if ($this->_database === null) {
            $coverageDatabase = $this->project
                                     ->getProperty('coverage.database');

            if (! $coverageDatabase) {
                throw new BuildException(
                    'Either include coverage-setup in your build file or set '
                    .'the "database" attribute'
                );
            }

            $database = new PhingFile($coverageDatabase);
        } else {
            $database = $this->_database;
        }

        $this->log('Calculating coverage threshold');

        $props = new Properties();
        $props->load($database);

        foreach ($props->keys() as $filename) {
            $file = unserialize($props->getProperty($filename));

            $this->calculateCoverageThreshold(
                $file['fullname'],
                $file['coverage']
            );
        }

        if ($this->_projectStatementCount > 0) {
            $coverage = (  $this->_projectStatementsCovered
                         / $this->_projectStatementCount) * 100;
        } else {
            $coverage = 0;
        }

        if ($coverage < $this->_perProject) {
            throw new BuildException(
                'The coverage (' . $coverage . '%) for the entire project '
                . 'is lower than the specified threshold ('
                . $this->_perProject . '%)'
            );
        }
    }
}