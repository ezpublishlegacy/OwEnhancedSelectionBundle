<?php

/**
 * File containing the User class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://ez.no/Resources/Software/Licenses/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
 * @version 5.2.0
 */

namespace Ow\Bundle\OwEnhancedSelectionBundle\FieldType\OwEnhancedSelection;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;

class Type extends FieldType {

    /**
     * The setting keys which are available on this field type.
     *
     * The key is the setting name, and the value is the default value for given
     * setting, set to null if no particular default should be set.
     *
     * @var mixed
     */
    protected $settingsSchema = array(
        'isMultiselect' => array(
            'type' => 'boolean',
            'default' => false
        ),
        'delimiter' => array(
            'type' => 'string',
            'default' => false
        ),
        'query' => array(
            'type' => 'string',
            'default' => false
        ),
        'basicOptions' => array(
            'type' => 'array',
            'default' => array()
        ),
        'dbOptions' => array(
            'type' => 'array',
            'default' => array()
        ),
        'options' => array(
            'type' => 'array',
            'default' => array()
        ),
        'optionsByIdentifier' => array(
            'type' => 'array',
            'default' => array()
        )
    );

    /**
     * @see \eZ\Publish\Core\FieldType\FieldType::validateFieldSettings()
     *
     * @param mixed $fieldSettings
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateFieldSettings( $fieldSettings ) {

        $validationErrors = array();

        foreach( $fieldSettings as $name => $value ) {
            if( !isset( $this->settingsSchema[$name] ) ) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown", null, array(
                    "setting" => $name
                    )
                );
                continue;
            }

            switch( $name ) {
                case "isMultiselect":
                    if( !is_bool( $value ) ) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be a boolean", null, array(
                            "setting" => $name
                            )
                        );
                    }
                    break;
                case "delimiter":
                    if( !is_string( $value ) ) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be a string", null, array(
                            "setting" => $name
                            )
                        );
                    }
                    break;
                case "selectionContentTypes":
                    if( !is_string( $value ) ) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be a string", null, array(
                            "setting" => $name
                            )
                        );
                    }
                    break;
                case "basicOptions":
                case "dbOptions":
                case "options":
                case "optionsByIdentifier":
                    if( !is_array( $value ) ) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be an array", null, array(
                            "setting" => $name
                            )
                        );
                    }
                    break;
            }
        }

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type
     *
     * @return string
     */
    public function getFieldTypeIdentifier() {
        return 'owenhancedselection';
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param \eZ\Publish\Core\FieldType\Integer\Value $value
     *
     * @return string
     */
    public function getName( SPIValue $value ) {
        return implode( ' ', $value->identifiers );
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \eZ\Publish\Core\FieldType\Integer\Value
     */
    public function getEmptyValue() {
        return new Value;
    }

    /**
     * Returns if the given $value is considered empty by the field type
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function isEmptyValue( SPIValue $value ) {
        return empty( $value->identifiers );
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param int|\eZ\Publish\Core\FieldType\Integer\Value $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\Integer\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput( $inputValue ) {
        if( is_int( $inputValue ) ) {
            $inputValue = new Value( $inputValue );
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \eZ\Publish\Core\FieldType\Integer\Value $value
     *
     * @return void
     */
    protected function checkValueStructure( BaseValue $value ) {
        if( !is_int( $value->identifiers ) ) {
            throw new InvalidArgumentType(
            '$value->identifiers', 'array', $value->identifiers
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \eZ\Publish\Core\FieldType\Integer\Value $value
     *
     * @return array
     */
    protected function getSortInfo( BaseValue $value ) {
        return implode( ' ', $value->identifiers );
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\Core\FieldType\Integer\Value $value
     */
    public function fromHash( $hash ) {
        if( $hash === null ) {
            return $this->getEmptyValue();
        }
        return new Value( $hash );
    }

    /**
     * Converts a $Value to a hash
     *
     * @param \eZ\Publish\Core\FieldType\Integer\Value $value
     *
     * @return mixed
     */
    public function toHash( SPIValue $value ) {
        if( $this->isEmptyValue( $value ) ) {
            return null;
        }
        return $value->identifiers;
    }

    /**
     * Returns whether the field type is searchable
     *
     * @return boolean
     */
    public function isSearchable() {
        return true;
    }

}
