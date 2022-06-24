<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Spreadsheets
 */
class Zend_Gdata_Spreadsheets_RowCountTest extends \PHPUnit\Framework\TestCase
{

    public function setUp(): void
    {
        $this->rowCount = new Zend_Gdata_Spreadsheets_Extension_RowCount();
    }

    public function testToAndFromString()
    {
        $this->rowCount->setText('20');
        $this->assertTrue($this->rowCount->getText() == '20');
        $newRowCount = new Zend_Gdata_Spreadsheets_Extension_RowCount();
        $doc = new DOMDocument();
        $doc->loadXML($this->rowCount->saveXML());
        $newRowCount->transferFromDom($doc->documentElement);
        $this->assertTrue($this->rowCount->getText() == $newRowCount->getText());
    }

}
