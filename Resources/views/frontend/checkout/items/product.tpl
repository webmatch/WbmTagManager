{extends file='parent:frontend/checkout/items/product.tpl'}

{block name='frontend_checkout_cart_item_delete_article_form_submit'}
    {s name="CartItemLinkDelete" assign="snippetCartItemLinkDelete"}{/s}
    <button type="submit" class="btn is--small column--actions-link cart-item-delete"
            title="{$snippetCartItemLinkDelete|escape}"
            data-ordernumber="{$sBasketItem.ordernumber}"
            data-quantity="{$sBasketItem.quantity}"
            data-price="{$sBasketItem.price}">
        <i class="icon--cross"></i>
    </button>
{/block}