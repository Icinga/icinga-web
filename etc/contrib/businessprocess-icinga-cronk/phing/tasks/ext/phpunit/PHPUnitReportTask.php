<?php
/**
 * $Id: PHPUnitReportTask.php 652 2009-12-14 11:31:28Z mrook $
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
require_once 'phing/system/io/FileWriter.php';
require_once 'phing/util/ExtendedFileStream.php';

/**
 * Transform a PHPUnit xml report using XSLT.
 * This transformation generates an html report in either framed or non-framed
 * style. The non-framed style is convenient to have a concise report via mail, 
 * the framed report is much more convenient if you want to browse into 
 * different packages or testcases since it is a Javadoc like report.
 *
 * @author Michiel Rook <michiel.rook@gmail.com>
 * @version $Id: PHPUnitReportTask.php 652 2009-12-14 11:31:28Z mrook $
 * @package phing.tasks.ext.phpunit
 * @since 2.1.0
 */
class PHPUnitReportTask extends Task
{
    private $format = "noframes";
    private $styleDir = "";
    private $toDir = "";

    /** the directory where the results XML can be found */
    private $inFile = "testsuites.xml";

    /**
     * Set the filename of the XML results file to use.
     */
    function setInFile($inFile)
    {
        $this->inFile = $inFile;
    }

    /**
     * Set the format of the generated report. Must be noframes or frames.
     */
    function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * Set the directory where the stylesheets are located.
     */
    function setStyleDir($styleDir)
    {
        $this->styleDir = $styleDir;
    }

    /**
     * Set the directory where the files resulting from the 
     * transformation should be written to.
     */
    function setToDir($toDir)
    {
        $this->toDir = $toDir;
    }
    
    /**
     * Returns the path to the XSL stylesheet
     */
    private function getStyleSheet()
    {
        $xslname = "phpunit-" . $this->format . ".xsl";
        
        if ($this->styleDir)
        {
            $file = new PhingFile($this->styleDir, $xslname);
        }
        else
        {
            $path = Phing::getResourcePath("phing/etc/$xslname");
            
            if ($path === NULL)
            {
                $path = Phing::getResourcePath("etc/$xslname");

                if ($path === NULL)
                {
                    throw new BuildException("Could not find $xslname in resource path");
                }
            }
            
            $file = new PhingFile($path);
        }

        if (!$file->exists())
        {
            throw new BuildException("Could not find file " . $file->getPath());
        }

        return $file;
    }
    
    /**
     * Transforms the DOM document
     */
    private function transform(DOMDocument $document)
    {
        $dir = new PhingFile($this->toDir);
        
        if (!$dir->exists())
        {
            throw new BuildException("Directory '" . $this->toDir . "' does not exist");
        }
        
        $xslfile = $this->getStyleSheet();

        $xsl = new DOMDocument();
        $xsl->load($xslfile->getAbsolutePath());

        $proc = new XSLTProcessor();
        $proc->importStyleSheet($xsl);

        if ($this->format == "noframes")
        {
            $writer = new FileWriter(new PhingFile($this->toDir, "phpunit-noframes.html"));
            $writer->write($proc->transformToXML($document));
            $writer->close();
        }
        else
        {
            ExtendedFileStream::registerStream();

            // no output for the framed report
            // it's all done by extension...
            $dir = new PhingFile($this->toDir);
            $proc->setParameter('', 'output.dir', $dir->toString());
            $proc->transformToXML($document);
            
            ExtendedFileStream::unregisterStream();
        }
    }
    
    /**
     * Fixes DOM document tree:
     *   - adds package="default" to 'testsuite' elements without
     *     package attribute
     *   - removes outer 'testsuite' container(s)
     */
    private function fixDocument(DOMDocument $document)
    {
        $rootElement = $document->firstChild;
        
        $xp = new DOMXPath($document);
        
        $nodes = $xp->query("/testsuites/testsuite");
        
        foreach ($nodes as $node)
        {
            $children = $xp->query("./testsuite", $node);
            
            foreach ($children as $child)
            {                
                if (!$child->hasAttribute('package'))
                {
                    $child->setAttribute('package', 'default');
                }
                
                $rootElement->appendChild($child);
            }
            
            $rootElement->removeChild($node);
        }
    }

    /**
     * The main entry point
     *
     * @throws BuildException
     */
    public function main()
    {
        $testSuitesDoc = new DOMDocument();
        $testSuitesDoc->load($this->inFile);
        
        $this->fixDocument($testSuitesDoc);
        
        $this->transform($testSuitesDoc);
    }
}
