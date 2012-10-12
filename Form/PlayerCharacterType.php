<?php
namespace Odl\ShadowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlayerCharacterType
    extends AbstractType
{
    public function __construct() {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
            'data_class' => 'Odl\ShadowBundle\Documents\PlayerCharacter',
            'error_bubbling' => false
        );
    }

    public function getName() {
    	return 'player_character';
    }
}
