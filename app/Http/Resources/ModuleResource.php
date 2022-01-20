<?php

namespace Crater\Http\Resources;

use Crater\Models\Module as ModelsModule;
use Crater\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;
use Nwidart\Modules\Facades\Module;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->checkPurchased();
        $this->installed_module = ModelsModule::where('name', $this->module_name)->first();

        return [
            'id' => $this->id,
            'average_rating' => $this->average_rating,
            'cover' => $this->cover,
            'slug' => $this->slug,
            'module_name' => $this->module_name,
            'faq' => $this->faq,
            'highlights' => $this->highlights,
            'installed_module_version' => $this->getInstalledModuleVersion(),
            'installed_module_version_updated_at' => $this->getInstalledModuleUpdatedAt(),
            'latest_module_version' => $this->latest_module_version->module_version,
            'latest_module_version_updated_at' => $this->latest_module_version->created_at,
            'is_dev' => $this->is_dev,
            'license' => $this->license,
            'long_description' => $this->long_description,
            'monthly_price' => $this->monthly_price,
            'name' => $this->name,
            'purchased' => $this->purchased,
            'reviews' => $this->reviews ?? [],
            'screenshots' => $this->screenshots,
            'short_description' => $this->short_description,
            'type' => $this->type,
            'yearly_price' => $this->yearly_price,
            'author_name' => $this->author->name,
            'author_avatar' => $this->author->avatar,
            'installed' => $this->moduleInstalled(),
            'enabled' => $this->moduleEnabled(),
            'update_available' => $this->updateAvailable(),
            'video_link' => $this->video_link,
            'video_thumbnail' => $this->video_thumbnail,
            'links' => $this->links
        ];
    }

    public function getInstalledModuleVersion()
    {
        if (isset($this->installed_module) && $this->installed_module->installed) {
            return $this->installed_module->version;
        }

        return null;
    }

    public function getInstalledModuleUpdatedAt()
    {
        if (isset($this->installed_module) && $this->installed_module->installed) {
            return $this->installed_module->updated_at;
        }

        return null;
    }

    public function moduleInstalled()
    {
        if (isset($this->installed_module) && $this->installed_module->installed) {
            return true;
        }

        return false;
    }

    public function moduleEnabled()
    {
        if (isset($this->installed_module) && $this->installed_module->installed) {
            return $this->installed_module->enabled;
        }

        return false;
    }

    public function updateAvailable()
    {
        if (! isset($this->installed_module)) {
            return false;
        }

        if (! $this->installed_module->installed) {
            return false;
        }

        if (! isset($this->latest_module_version)) {
            return false;
        }

        if (version_compare($this->installed_module->version, $this->latest_module_version->module_version, '>=')) {
            return false;
        }

        if (version_compare(Setting::getSetting('version'), $this->latest_module_version->crater_version, '<')) {
            return false;
        }

        return true;
    }

    public function checkPurchased()
    {
        if ($this->purchased) {
            return true;
        }

        if (Module::has($this->module_name)) {
            $module = Module::find($this->module_name);
            $module->disable();
            ModelsModule::where('name', $this->module_name)->update(['enabled' => false]);
        }

        return false;
    }
}
