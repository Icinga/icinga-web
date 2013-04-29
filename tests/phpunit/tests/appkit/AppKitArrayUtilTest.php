<?php

class AppKitArrayUtilTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group AppKit
     */
    public function testSearchKeyRecursive()
    {
        $test = array(
            'partA' => array(
                'partAA' => array(
                    'partAAA' => true,
                    'partAAB' => true
                )
            )
        );

        $this->assertTrue(AppKitArrayUtil::searchKeyRecursive('partAAA', $test));
        $this->assertTrue(AppKitArrayUtil::searchKeyRecursive('partAA', $test));
        $this->assertFalse(AppKitArrayUtil::searchKeyRecursive('partNOT', $test));
    }

    /**
     * @group AppKit
     */
    public function testFlattenArray()
    {
        $test = array(
            'test1' => 'OK',
            'test2' => 'OK',
            'test3' => array(
                'test3.1' => 'OK',
                'test3.2' => 'OK'
            )
        );

        $flat = AppKitArrayUtil::flattenArray($test, 'pref');

        $this->assertArrayHasKey('pref.test2', $flat);
        $this->assertArrayHasKey('pref.test3.test3.1', $flat);
        $this->assertArrayHasKey('pref.test3.test3.2', $flat);
    }

    /**
     * @group AppKit
     */
    public function testUniqueKeysArray()
    {

        $test = array(
            'test1' => 'OK',
            'test2' => array(
                'test3' => 'OK',
                'test4' => 'OK',
                'test5' => array(
                    'test6' => 'OK',
                    'test7' => 'OK'
                )
            )
        );

        $flat = AppKitArrayUtil::uniqueKeysArray($test);

        $this->assertArrayHasKey('test1', $flat);
        $this->assertArrayHasKey('test3', $flat);
        $this->assertArrayHasKey('test6', $flat);
        $this->assertArrayHasKey('test4', $flat);

        $this->assertArrayNotHasKey('test2', $flat);
        $this->assertArrayNotHasKey('test5', $flat);

    }

    /**
     * @group AppKit
     */
    public function testUniqueMultidimensional()
    {

        $test = array(
            array('row1_a', 'row1_b', 'row1_c'),
            array('row1_a', 'row1_b', 'row1_c'),
            array('row1_a', 'row1_b', 'row1_c'),
            array('row2_a', 'row2_b', 'row2_c'),
            array('row3_a', 'row3_b', 'row3_c'),
            array('row2_a', 'row2_b', 'row2_c'),
            array('row3_a', 'row3_b', 'row3_c'),
            array('row1_a', 'row1_b', 'row1_c')
        );

        $unique = array_values(AppKitArrayUtil::uniqueMultidimensional($test));

        $this->assertCount(3, $unique);
        $this->assertEquals($unique[1], array('row2_a', 'row2_b', 'row2_c'));
    }

    /**
     * @group AppKit
     */
    public function testTrimSplit()
    {
        $string = 'test,      laola       , OK   DING                     ,';
        $splitted = AppKitArrayUtil::trimSplit($string);

        $this->assertEquals('test', $splitted[0]);
        $this->assertEquals('OK   DING', $splitted[2]);
    }

    /**
     * @group AppKit
     */
    public function testSubSort()
    {
        $test = array(
            array('field1' => 'Z', 'field2' => 0),
            array('field1' => 'Y', 'field2' => 1),
            array('field1' => 'X', 'field2' => 2),
            array('field1' => 'W', 'field2' => 3),
            array('field1' => 'V', 'field2' => 4)
        );

        AppKitArrayUtil::subSort($test, 'field1', '<');
        $test = array_values($test); // RESET KEYS

        $this->assertEquals('V', $test[0]['field1']);
        $this->assertEquals(4, $test[0]['field2']);

        $this->assertEquals('Z', $test[4]['field1']);
        $this->assertEquals(0, $test[4]['field2']);
    }

    /**
     * @group AppKit
     */
    public function testXml2Array()
    {

        $xml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
        <root>
            <param name="DING1">DONG</param>
            <param name="DING2">
                DONG
            </param>
            <param name="DING3">DONG</param>
            <param name="DING4">
                <param name="DING4.1">
                    <![CDATA[
                    LA
                    OLA!
                    ]]>
                </param>
                <param name="DING4.2">DONG</param>
            </param>
        </root>
EOT;

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml);
        $root = $dom->getElementsByTagName('root');
        AppKitArrayUtil::xml2Array($root, $dump);

        $this->assertCount(1, $dump);
        $this->assertCount(4, $dump['root']);
        $this->assertEquals('DONG', $dump['root']['DING4']['DING4.2']);
    }

    /**
     * @group AppKit
     */
    public function testMatchAgainstStringList()
    {
        $list='item1, item123123, item';
        $this->assertTrue(AppKitArrayUtil::matchAgainstStringList($list, 'item1'));
        $this->assertTrue(AppKitArrayUtil::matchAgainstStringList($list, 'item'));
        $this->assertFalse(AppKitArrayUtil::matchAgainstStringList($list, 'item12'));
    }

    /**
     * @group AppKit
     */
    public function testReplaceRecursive()
    {
        $base = array('citrus' => array( "orange") , 'berries' => array("blackberry", "raspberry"), );
        $replacements = array('citrus' => array('pineapple'), 'berries' => array('blueberry'));

        $test = AppKitArrayUtil::replaceRecursive($base, $replacements);

        $this->assertEquals('pineapple', $test['citrus'][0]);
        $this->assertEquals(array('blueberry', 'raspberry'), $test['berries']);
    }

    /**
     * @group AppKit
     */
    public function testSwapKeys()
    {
        $test = array(
            'test1' => 'OK',
            'test2' => 'OK',
            'test3' => 'OK'
        );

        $test2 = $test;

        $rewrite = array(
            'test2' => 'LAOLA2'
        );

        AppKitArrayUtil::swapKeys($test, $rewrite, true);
        AppKitArrayUtil::swapKeys($test2, $rewrite, false);

        $this->assertCount(1, $test);
        $this->assertArrayHasKey('LAOLA2', $test);
        $this->assertArrayNotHasKey('test3', $test);
        $this->assertArrayNotHasKey('test1', $test);

        $this->assertCount(3, $test2);
        $this->assertArrayHasKey('LAOLA2', $test2);
        $this->assertArrayHasKey('test3', $test2);
    }

    /**
     * @group AppKit
     */
    public function testEncodingProcessor1()
    {
        $test = array (
            'test1' => 'ÜÜöö%%123',
            'test2' => array('ÖÖÖ', 'ÄÄÄ', 'ßßß')
        );

        $work = $test;

        AppKitArrayUtil::toISO_recursive($work);
        $this->assertNotEquals($test, $work);
        AppKitArrayUtil::toUTF8_recursive($work);
        $this->assertEquals($test, $work);
    }
}