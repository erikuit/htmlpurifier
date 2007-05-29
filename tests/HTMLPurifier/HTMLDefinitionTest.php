<?php

require_once 'HTMLPurifier/HTMLDefinition.php';

class HTMLPurifier_HTMLDefinitionTest extends UnitTestCase
{
    
    function test_parseTinyMCEAllowedList() {
        
        $def = new HTMLPurifier_HTMLDefinition();
        
        $this->assertEqual(
            $def->parseTinyMCEAllowedList('a,b,c'),
            array(array('a' => true, 'b' => true, 'c' => true), array())
        );
        
        $this->assertEqual(
            $def->parseTinyMCEAllowedList('a[x|y|z]'),
            array(array('a' => true), array('a.x' => true, 'a.y' => true, 'a.z' => true))
        );
        
        $this->assertEqual(
            $def->parseTinyMCEAllowedList('*[id]'),
            array(array(), array('*.id' => true))
        );
        
        $this->assertEqual(
            $def->parseTinyMCEAllowedList('a[*]'),
            array(array('a' => true), array('a.*' => true))
        );
        
        $this->assertEqual(
            $def->parseTinyMCEAllowedList('span[style],strong,a[href|title]'),
            array(array('span' => true, 'strong' => true, 'a' => true),
            array('span.style' => true, 'a.href' => true, 'a.title' => true))
        );
        
    }
    
    function test_Allowed() {
        
        $config1 = HTMLPurifier_Config::create(array(
            'HTML.AllowedElements' => array('b', 'i', 'p', 'a'),
            'HTML.AllowedAttributes' => array('a.href', '*.id')
        ));
        
        $config2 = HTMLPurifier_Config::create(array(
            'HTML.Allowed' => 'b,i,p,a[href],*[id]'
        ));
        
        $this->assertEqual($config1->getHTMLDefinition(), $config2->getHTMLDefinition());
        
    }
    
}

?>