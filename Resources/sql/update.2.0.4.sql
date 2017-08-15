UPDATE `wbm_data_layer_properties`
SET `module` = 'frontend_checkout_finish', `parentID` = 62, `name` = 'purchase', `value` = ''
WHERE `id` = 106 AND `name` = 'products';

INSERT IGNORE INTO `wbm_data_layer_properties` (`id`, `module`, `parentID`, `name`, `value`) VALUES
  (107, 'frontend_detail_index', 13, 'products', '[$sArticle] as $article');

UPDATE `wbm_data_layer_properties`
SET `parentID` = 107
WHERE `id` IN (16, 17, 18, 19, 21);