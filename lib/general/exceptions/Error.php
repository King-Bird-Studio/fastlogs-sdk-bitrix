<?php

namespace KingbirdFastlog\exceptions;

class Error
{
    const PARAMETER_NOT_DEFINED_MESSAGE = 'Parameter `%s` need to be defined as environment `%s` or passed as an argument of construcor';
    const PARAMETER_SLUG_NOT_DEFINED_CODE = 1003;
    const PARAMETER_URL_NOT_DEFINED_CODE = 1004;

    const RESOURCE_TYPE_MESSAGE = 'Resource type cannot be sended into log';
    const RESOURCE_TYPE_CODE = 1001;

    const UNCONVERTED_INPUT_MESSAGE = 'Input data cannot be converted into json';
    const UNCONVERTED_INPUT_CODE = 1002;
}
