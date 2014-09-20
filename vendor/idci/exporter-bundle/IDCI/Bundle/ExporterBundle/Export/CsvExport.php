<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Export;

class CsvExport extends AbstractExport
{
    protected $contentType = 'text/csv';


    public function buildHeader($header = null)
    {
      if(isset($header['options']) && isset($header['options']['header']) )
      {
        $this->addContent($header['options']['header'].PHP_EOL);
      }
      // $this->setContent('BEGIN:VCALENDAR'.PHP_EOL.'VERSION:2.0'.PHP_EOL.'PRODID:-//hacksw/handcal//NONSGML v1.0//EN'.PHP_EOL.PHP_EOL);
    }

    /**
     * addContent
     *
     * @param string $content
     */
    public function addContent($content)
    {
        $content = trim(preg_replace('/\s+/', ' ', $content));
        parent::addContent($content.PHP_EOL);
    }
}
