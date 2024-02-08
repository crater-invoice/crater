<?php

namespace InvoiceShelf\Traits;

trait GeneratesMenuTrait
{
    public function generateMenu($key, $user)
    {
        $new_items = [];

        $menu = \Menu::get($key);
        $items = $menu ? $menu->items->toArray() : [];

        foreach ($items as $data) {
            if ($user->checkAccess($data)) {
                $new_items[] = [
                    'title' => $data->title,
                    'link' => $data->link->path['url'],
                    'icon' => $data->data['icon'],
                    'name' => $data->data['name'],
                    'group' => $data->data['group'],
                ];
            }
        }

        return $new_items;
    }
}
