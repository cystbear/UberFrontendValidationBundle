<?php

namespace Sleepness\UberFrontendValidationBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validator;

/**
 * From extension what make available client side validation,
 * by getting entity metadata and pass it in form theme
 *
 * @author Viktor Novikov <viktor.novikov95@gmail.com>
 * @author Alexandr Zhulev <alexandrzhulev@gmail.com>
 */
class UberFrontendValidationFormExtension extends AbstractTypeExtension
{
    /**
     * @var \Symfony\Component\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * Set validator service to be able to get entity metadata
     *
     * @param $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $fieldName = $view->vars['full_name'];
        $parentForm = $form->getParent();
        if ($parentForm != null) {
            $config = $parentForm->getConfig();
            $validationGroups = $config->getOptions()['validation_groups'];
            $dataClass = $config->getDataClass();
            $entityMetadata = ($dataClass != null) ? $entityMetadata = $this->validator->getMetadataFor($dataClass) : null;
            $view->vars['entity_constraints'] = $this->prepareConstraintsAttributes($fieldName, $entityMetadata, $validationGroups);
        }
    }

    /**
     * Prepare array of constraints based on entity metadata
     *
     * @param $fieldName        - name of form field
     * @param $entityMetadata   - entity metadata
     * @param $validationGroups - form validation groups
     * @return array            - prepared constraints for given field
     */
    private function prepareConstraintsAttributes($fieldName, $entityMetadata, $validationGroups)
    {
        $result = array();
        $start = strrpos($fieldName, '[') + 1;
        $finish = strrpos($fieldName, ']');
        $length = $finish - $start;
        $parsedFieldName = substr($fieldName, $start, $length);
        if ($entityMetadata != null) {
            $entityProperties = $entityMetadata->properties;
            foreach ($entityProperties as $property => $credentials) {
                if ($property == $parsedFieldName) {
                    if (($validationGroups != null)) {
                        if (in_array($validationGroups[0], array_keys($credentials->constraintsByGroup))) {
                            $constraints = $entityProperties[$property]->constraints;
                            foreach ($constraints as $constraint) {
                                $partsOfConstraintName = explode('\\', get_class($constraint));
                                $constraintName = end($partsOfConstraintName);
                                foreach ($constraint as $constraintProperty => $constraintValue) {
                                    $result[$fieldName][$constraintName][$constraintProperty] = $constraintValue;
                                }
                            }
                        }
                    } else {
                        $constraints = $entityProperties[$property]->constraints;
                        foreach ($constraints as $constraint) {
                            $partsOfConstraintName = explode('\\', get_class($constraint));
                            $constraintName = end($partsOfConstraintName);
                            foreach ($constraint as $constraintProperty => $constraintValue) {
                                $result[$fieldName][$constraintName][$constraintProperty] = $constraintValue;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'field';
    }
} 
