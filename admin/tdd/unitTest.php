<?php
use PHPUnit\Framework\TestCase;
class UnitTest extends TestCase
{
    public $menuClass;
    
    public function setUp()
    {
        $this->menuClass =  new Menu();
    }
    protected function tearDown()
    {
        $this->menuClass = NULL;
    }
    public function menuProvider()
    {
        return array(
            array('003')
        );
    }
    public function saveMenuProvider()
    {
        return array(
            array('003')
        );
    }
    /**
     * @dataProvider menuProvider
     */
    public function testIfMenuCanBeGenerated($role)
    {
        $this->assertNotEmpty($this->menuClass->generateMenu($role));
        $this->assertArrayHasKey('menu_id',$this->menuClass->generateMenu($role)['data'][0]);
        $this->assertArrayHasKey('response_code',$this->menuClass->generateMenu($role));
    }
    public function testIfMenuIsSaved()
    {
        $this->assertArrayHasKey('response_code',$this->menuClass->generateMenu($role));
    }
    
}
