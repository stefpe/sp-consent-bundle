<?php

namespace Stefpe\SpConsentBundle\Enum;

enum ConsentAction: string
{
    case ACCEPT_ALL = 'accept_all';
    case REJECT_OPTIONAL = 'reject_optional';
    case CUSTOM = 'custom';
}

