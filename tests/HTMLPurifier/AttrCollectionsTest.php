<?php

require_once 'HTMLPurifier/AttrCollections.php';

Mock::generatePartial(
    'HTMLPurifier_AttrCollections',
    'HTMLPurifier_AttrCollections_TestForConstruct',
    array('performInclusions', 'expandIdentifiers')
);

class HTMLPurifier_AttrCollectionsTest extends UnitTestCase
{
    
    function testConstruction() {
        
        generate_mock_once('HTMLPurifier_AttrTypes');
        
        $collections = new HTMLPurifier_AttrCollections_TestForConstruct($this);
        
        $types = new HTMLPurifier_AttrTypesMock($this);
        
        $modules = array();
        
        $modules['Module1'] = new HTMLPurifier_HTMLModule();
        $modules['Module1']->attr_collections = array(
            'Core' => array(
                0 => array('Soup', 'Undefined'),
                'attribute' => 'Type',
                'attribute-2' => 'Type2',
            ),
            'Soup' => array(
                'attribute-3' => 'Type3-old' // overwritten
            )
        );
        
        $modules['Module2'] = new HTMLPurifier_HTMLModule();
        $modules['Module2']->attr_collections = array(
            'Core' => array(
                0 => array('Brocolli')
            ),
            'Soup' => array(
                'attribute-3' => 'Type3'
            ),
            'Brocolli' => array()
        );
        
        $collections->HTMLPurifier_AttrCollections($types, $modules);
        // this is without identifier expansion or inclusions
        $this->assertIdentical(
            $collections->info,
            array(
                'Core' => array(
                    0 => array('Soup', 'Undefined', 'Brocolli'),
                    'attribute' => 'Type',
                    'attribute-2' => 'Type2'
                ),
                'Soup' => array(
                    'attribute-3' => 'Type3'
                ),
                'Brocolli' => array()
            )
        );
        
    }
    
    function test_performInclusions() {
        
        generate_mock_once('HTMLPurifier_AttrTypes');
        
        $types = new HTMLPurifier_AttrTypesMock($this);
        $collections = new HTMLPurifier_AttrCollections($types, array());
        $collections->info = array(
            'Core' => array(0 => array('Inclusion', 'Undefined'), 'attr-original' => 'Type'),
            'Inclusion' => array(0 => array('SubInclusion'), 'attr' => 'Type'),
            'SubInclusion' => array('attr2' => 'Type')
        );
        
        $collections->performInclusions($collections->info['Core']);
        $this->assertIdentical(
            $collections->info['Core'],
            array(
                'attr-original' => 'Type', 
                'attr' => 'Type',
                'attr2' => 'Type'
            )
        );
        
        // test recursive
        $collections->info = array(
            'One' => array(0 => array('Two'), 'one' => 'Type'),
            'Two' => array(0 => array('One'), 'two' => 'Type')
        );
        $collections->performInclusions($collections->info['One']);
        $this->assertIdentical(
            $collections->info['One'],
            array(
                'one' => 'Type',
                'two' => 'Type'
            )
        );
        
    }
    
    function test_expandIdentifiers() {
        
        generate_mock_once('HTMLPurifier_AttrTypes');
        
        $types = new HTMLPurifier_AttrTypesMock($this);
        $collections = new HTMLPurifier_AttrCollections($types, array());
        
        $attr = array(
            'attr1' => 'Color',
            'attr2' => 'URI'
        );
        $types->setReturnValue('get', 'ColorObject', array('Color'));
        $types->setReturnValue('get', 'URIObject', array('URI'));
        $collections->expandIdentifiers($attr, $types);
        
        $this->assertIdentical(
            $attr,
            array(
                'attr1' => 'ColorObject',
                'attr2' => 'URIObject'
            )
        );
        
    }
    
}

?>