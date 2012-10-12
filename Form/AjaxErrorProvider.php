<?php
namespace Odl\ShadowBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Translation\TranslatorInterface;

class AjaxErrorProvider
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getErrors(Form $form, &$errors = array(), $formView = null)
    {
        if (!$formView) {
            $formView = $form->createView();
        }

        $vars = $formView->vars;
        $id = isset($vars['id']) ? $vars['id'] : '*';

        if (!$form->isValid()) {
            // User translator to translate
            $errorObjects = $form->getErrors();

            foreach ($errorObjects as $errorObject) {
                $errors[$id][] = $this->translator
                        ->trans($errorObject->getMessageTemplate(),
                                $errorObject->getMessageParameters(),
                                'validators');
            }
        } else {
            $errors[$id] = null;
        }

        foreach ($formView as $childName => $childFormView) {
            if (isset($form[$childName])) {
                $this->getErrors($form[$childName], $errors, $childFormView);
            }
        }

        return $errors;
    }
}