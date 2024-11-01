<?php

defined( 'ABSPATH' ) || exit;

function smartcustomizer_define_constant($name, $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

smartcustomizer_define_constant( 'SMARTCUSTOMIZER_VERSION', '1.1');
smartcustomizer_define_constant( 'SMARTCUSTOMIZER_URL', 'https://app.smartcustomizer.com/');
smartcustomizer_define_constant( 'SMARTCUSTOMIZER_MODE', 'live');
