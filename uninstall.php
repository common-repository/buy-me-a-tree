<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_name = 'bmat_options';

delete_option($option_name);