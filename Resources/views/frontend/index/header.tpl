{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_meta_http_tags"}
    {$smarty.block.parent}
    {if {config name='wbmTagManagerCookieConsent'}}
        <script>
            {literal}
            var googleTag = function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+'{/literal}{config name='wbmExtendedURLParameter'}{literal}';f.parentNode.insertBefore(j,f);};
            {/literal}
        </script>
    {/if}
{/block}