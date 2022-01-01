<?php

namespace Crater\Traits;

trait GeneratesMenuTrait
{
    public function generateMenu($key, $user)
    {
        $menu = [];

        foreach (\Menu::get($key)->items->toArray() as $data) {
            if ($user->checkAccess($data)) {
                $menu[] = [
                    'title' => $data->title,
                    'link' => $data->link->path['url'],
                    'icon' => $data->data['icon'],
                    'name' => $data->data['name'],
                    'group' => $data->data['group'],
                ];
            }
        }

        return $menu;
    }
}
