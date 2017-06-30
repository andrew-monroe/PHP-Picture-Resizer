<?php
  // account
  $config_access_key_id               = 'AWS_ACCESS_KEY_ID';
  $config_secret_access_key           = 'AWS_SECRET_ACCESS_KEY';

  // service
  $config_service                     = 's3';

  // s3 buckets
  $config_POST_region                 = 'us-east-1';
  $config_POST_bucket                 = 'input_bucket';
  $config_GET_region                  = 'us-east-1';
  $config_GET_bucket                  = 'output_bucket';

  // time limits in seconds
  $config_GET_expiration_time_limit   = "86400";  // 24 hours
  $config_POST_expiration_time_limit  = "3600";   // 1 hour

  // conditions
  $config_key_starts_with             = 'uploads/tmp/';
  $config_acl                         = 'private';
?>
