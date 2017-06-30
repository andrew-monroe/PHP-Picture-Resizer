<?php
  // account
  $config_access_key_id               = 'AKIAIXES3V2PY4VAZ2ZA';
  $config_secret_access_key           = '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';

  // service
  $config_service                     = 's3';

  // s3 buckets
  $config_POST_region                 = 'us-east-1';
  $config_POST_bucket                 = 'andy-bnb-test';
  $config_GET_region                  = 'us-east-1';
  $config_GET_bucket                  = 'andy-bnb-testresized';

  // time limits in seconds
  $config_GET_expiration_time_limit   = "86400";  // 24 hours
  $config_POST_expiration_time_limit  = "3600";   // 1 hour

  // conditions
  $config_key_starts_with             = 'uploads/tmp/';
  $config_acl                         = 'private';
?>
