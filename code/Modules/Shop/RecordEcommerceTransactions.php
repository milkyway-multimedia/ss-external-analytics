<?php namespace Milkyway\SS\ExternalAnalytics\Modules\Userforms;

/**
 * Milkyway Multimedia
 * Controller.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Extension;
use Controller;

class RecordEcommerceTransactions extends Extension {
    public function afterAdd($item, $buyable, $quantity, $filter)
    {
        singleton('ea')->queue(
            'ecommerce',
            array_merge(
                $this->getParamsFromItem($item, $buyable, $quantity, $filter),
                [
                    'action' => 'addToCart',
                    'product_action' => 'add',
                ]
            ),
            '',
            Controller::curr()
        );
    }

    public function afterRemove($item, $buyable, $quantity, $filter)
    {
        singleton('ea')->queue(
            'ecommerce',
            array_merge(
                $this->getParamsFromItem($item, $buyable, $quantity, $filter),
                [
                    'action' => 'removeFromCart',
                    'product_action' => 'remove',
                ]
            ),
            '',
            Controller::curr()
        );
    }

    public function afterSetQuantity($item, $buyable, $quantity, $filter)
    {
        singleton('ea')->queue(
            'ecommerce',
            array_merge(
                $this->getParamsFromItem($item, $buyable, $quantity, $filter),
                [
                    'action' => $item->Quantity > $quantity ? 'removeFromCart' : 'addToCart',
                    'product_action' => $item->Quantity > $quantity ? 'add' : 'remove',
                ]
            ),
            '',
            Controller::curr()
        );
    }

    public function onPayment()
    {
        singleton('ea')->queue(
            'ecommerce',
            array_merge(
                $this->getParams(),
                [
                    'action' => 'transaction',
                ]
            ),
            '',
            Controller::curr()
        );
    }

    protected function getParams() {
        $params = [
            'id' => $this->owner->Reference ?: $this->owner->ID,
        ];

        foreach($this->owner->Items() as $item) {
            $params['items'][] = $this->getParamsFromItem($item);
        }

        return $params;
    }

    protected function getParamsFromItem($item, $buyable = null, $quantity = null, $filter = []) {
        if(!$buyable)
            $buyable = $item->Buyable();

        $params = [
            'id' => $item->ID,
            'name' => $buyable ? $buyable->Title : $item->Title,
            'price' => $item->CartPrice ?: $item->UnitPrice(),
            'quantity' => ($quantity !== null) ? $quantity : $item->Quantity,
        ];

        if($buyable && $buyable->Model)
            $params['brand'] = $buyable->Model;

        if($buyable && $buyable->InternalItemID)
            $params['sku'] = $buyable->InternalItemID;

        if($buyable && $buyable->Categories && ($category = $buyable->Categories->first())) {
            if($category->hasExtension('Hierarchy')) {
                $params['product_category'] = implode('/', array_map(function($item) {
                    return $item->Title;
                }, $category->parentStack()));
            }
            else {
                $params['product_category'] = $category->Title;
            }
        }

        if($buyable && ($buyable instanceof \ProductVariation)) {
            $params['variant'] = implode('/', $buyable->AttributeValues()->column('Title'));
        }

        if(\ShopConfig::get_site_currency()) {
            $params['currency'] = \ShopConfig::get_site_currency();
        }

        return $params;
    }
}