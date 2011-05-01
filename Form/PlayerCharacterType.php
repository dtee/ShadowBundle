<?php
namespace Odl\ShadowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PlayerCharacterType
	extends AbstractType
{
	public function __construct() {
	}

	public function buildForm(FormBuilder $builder, array $options)
    {
    	$builder
    		->add('username')
    		->add('character', 'text')
    		->add('isWin', 'checkbox',
    			array('required' => false, 'label' => 'Won?',))
    		->add('isAlive', 'checkbox',
    			array('required' => false, 'label' => 'Alive?',))
    		->add('isLastDeath', 'checkbox',
    			array('required' => false, 'label' => 'Last Death?'));
    }

	public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Odl\Shadowbundle\Documents\PlayerCharacter',
        	'error_bubbling' => false
        );
    }
}