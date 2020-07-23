{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_javascript_async_ready"}
    {if {config name='wbmTagManagerCookieConsent'}}
        <script>
            {literal}
            var googleTag = function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+'{/literal}{config name='wbmExtendedURLParameter'}{literal}';f.parentNode.insertBefore(j,f);};
            {/literal}
            document.asyncReady(function () {
                if ($.getCookiePreference('wbm_tag_manager')) {
                    googleTag(window,document,'script','dataLayer','{"{config name='wbmTagManagerContainer'}"|escape:'javascript'}');
                    googleTag = function () { };
                }
                $.subscribe('plugin/swCookieConsentManager/onBuildCookiePreferences', function (event, plugin, preferences) {
                    if ($.getCookiePreference('wbm_tag_manager')) {
                        googleTag(window,document,'script','dataLayer','{"{config name='wbmTagManagerContainer'}"|escape:'javascript'}');
                        googleTag = function () { };
                    }
                });
            });
        </script>
    {/if}
    {$smarty.block.parent}
{/block}
