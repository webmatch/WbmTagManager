ALTER TABLE `wbm_data_layer_modules` ADD UNIQUE INDEX `module_UNIQUE` (`module` ASC);

ALTER TABLE `wbm_data_layer_modules`
ADD `predispatch` BOOLEAN DEFAULT '0';

UPDATE `wbm_data_layer_modules`
SET `predispatch` = 1
WHERE `module` = 'frontend_checkout_ajaxdeletearticlecart';

UPDATE `wbm_data_layer_properties`
SET `value` = '{if $sCategoryContent.name}{$sCategoryContent.name|escape}{elseif $smarty.request.c}{dbquery select=\'description\' from=\'s_categories\' where=[\'id =\' => $smarty.request.c]}{/if}'
WHERE `id` = 10 AND `name` = 'category';

UPDATE `wbm_data_layer_properties`
SET `value` = '{dbquery select=\'ordernumber\' from=\'s_order_basket\' where=[\'id =\' => {request_get param=\'sDelete\'}]}'
WHERE `id` = 38 AND `name` = 'id';