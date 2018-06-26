ALTER TABLE `wbm_data_layer_modules` ADD UNIQUE INDEX `module_UNIQUE` (`module` ASC);

UPDATE `wbm_data_layer_properties`
SET `value` = '{if $sCategoryContent.name}{$sCategoryContent.name|escape}{elseif $smarty.request.c}{dbquery select=\'description\' from=\'s_categories\' where=[\'id =\' => $smarty.request.c]}{/if}'
WHERE `id` = 10 AND `name` = 'category';