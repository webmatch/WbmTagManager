ALTER TABLE `wbm_data_layer_modules` ADD UNIQUE INDEX `module_UNIQUE` (`module` ASC);

ALTER TABLE `wbm_data_layer_modules`
ADD `predispatch` BOOLEAN DEFAULT '0';

UPDATE `wbm_data_layer_modules`
SET `predispatch` = 1
WHERE `module` = 'frontend_checkout_ajaxdeletearticlecart';

UPDATE `wbm_data_layer_properties`
SET `value` = '{$sArticle.ordernumber|escape|to_string}'
WHERE `id` = 7 AND `value` = '{$sArticle.ordernumber|escape}';

UPDATE `wbm_data_layer_properties`
SET `value` = '{$article.ordernumber|escape|to_string}'
WHERE `id` = 17 AND `value` = '{$article.ordernumber|escape}';

UPDATE `wbm_data_layer_properties`
SET `value` = '{if $sCategoryContent.name}{$sCategoryContent.name|escape}{elseif $smarty.request.c}{{dbquery select=\'description\' from=\'s_categories\' where=[\'id =\' => $smarty.request.c]}|escape}{/if}'
WHERE `id` = 10 AND `value` = '{$sCategoryContent.name|escape}';

UPDATE `wbm_data_layer_properties`
SET `value` = '{{dbquery select=\'ordernumber\' from=\'s_order_basket\' where=[\'id =\' => {request_get param=\'sDelete\'}]}|escape|to_string}'
WHERE `id` = 38 AND `value` = '{$smarty.request.sDelete}';

INSERT IGNORE INTO `wbm_data_layer_properties` (`id`, `module`, `parentID`, `name`, `value`) VALUES
  (108, 'frontend_checkout_ajaxaddarticlecart', 28, 'price', '{dbquery select=\'price\' from=\'s_order_basket\' where=[\'ordernumber =\' => $smarty.request.sAdd, \'sessionID =\' => $smarty.session.Shopware.sessionId] order=[\'id\' => \'DESC\']}'),
  (109, 'frontend_checkout_ajaxdeletearticlecart', 37, 'price', '{dbquery select=\'price\' from=\'s_order_basket\' where=[\'id =\' => {request_get param=\'sDelete\'}]}'),
  (110, 'frontend_checkout_ajaxdeletearticlecart', 37, 'quantity', '{dbquery select=\'quantity\' from=\'s_order_basket\' where=[\'id =\' => {request_get param=\'sDelete\'}]}');