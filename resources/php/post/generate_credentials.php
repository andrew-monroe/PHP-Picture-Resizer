<?php
  include_once 'shared.php';
  include_once '../configs/config.php';

  $POST_file_key        = file_key(       $config_key_starts_with);
  $short_date           = short_date();
  $long_date            = long_date();
  $POST_expiration_date = expiration_date($config_POST_expiration_time_limit);

  $string_to_sign       = string_to_sign( $config_access_key_id,
                                          $POST_expiration_date,
                                          $short_date,
                                          $long_date,
                                          $config_POST_bucket,
                                          $config_key_starts_with,
                                          $config_acl);

  $signing_key          = signing_key(    $config_secret_access_key,
                                          $short_date);

  $signature            = signature(      $string_to_sign,
                                          $signing_key);

  echo json(                              $string_to_sign,
                                          $signature,
                                          $config_access_key_id,
                                          $POST_file_key,
                                          $config_POST_bucket,
                                          $short_date,
                                          $long_date,
                                          $config_acl);
?>
