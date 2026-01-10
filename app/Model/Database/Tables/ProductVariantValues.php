<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for product variant values table
 */
enum ProductVariantValues: string{
    /**
     * Name of the product variant values table
     */
    case Table = 'product_variant_values';
    /**
     * ID of the product variant value
     */
    case Id = 'id';
    /**
     * ID of the product variant this value belongs to
     */
    case ProductVariantId = 'product_variant_id';
    /**
     * ID of the attribute value assigned to the product variant
     */
    case AttributeValueId = 'attribute_value_id';
}