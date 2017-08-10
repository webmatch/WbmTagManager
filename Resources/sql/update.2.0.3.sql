INSERT IGNORE INTO `wbm_data_layer_properties` (`id`, `module`, `parentID`, `name`, `value`) VALUES
  (106, 'frontend_detail_index', 13, 'products', '[$sArticle] as $article');

UPDATE `wbm_data_layer_properties`
  SET `parentID` = 106, `value` = REPLACE(`value`, '$sArticle', '$article')
  WHERE `id` IN (16, 17, 18, 19, 21);