<?php

namespace App\Exception;

use Exception;

class ProductCantBeCreatedException extends Exception
{
    const MESSAGE_PRODUCT_EXISTS = 'Product with same attributes already exists';
    const MESSAGE_PRODUCT_COLOR_DOES_NOT_EXISTS = 'Product color does not exist';
    const MESSAGE_PRODUCT_TYPE_DOES_NOT_EXISTS = 'Product type does not exist';
    const MESSAGE_PRODUCT_SIZE_DOES_NOT_EXISTS = 'Product size does not exist';
}