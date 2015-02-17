<?php

namespace Sleepness\UberFrontendValidationBundle\Twig\Extension;

/**
 * Extension, that provide some base twig functions for including javascript files, etc.
 */
class UberFrontendValidationTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param $twig
     */
    public function setTemplating($twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('validation_init', 'getValidators', array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('pure_field_name', 'getFieldName', array('is_safe' => array('html'))),
        );
    }

    /**
     * Will return including all javascript files for validation
     */
    public function getValidators()
    {
        return $this->twig->render('SleepnessUberFrontendValidationBundle::validators.html.twig');
    }

    /**
     * Return pure field name
     *
     * @param $field
     * @return string
     */
    public function getFieldName($field)
    {
        $start = "[";
        $end = "]";
        $string = " " . $field;
        $ini = strpos($string, $start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * Return name of extension
     *
     * @return string - name of extension
     */
    public function getName()
    {
        return 'uber_frontend_validation';
    }
} 
