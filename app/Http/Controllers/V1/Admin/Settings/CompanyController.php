<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Settings;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\AvatarRequest;
use InvoiceShelf\Http\Requests\CompanyLogoRequest;
use InvoiceShelf\Http\Requests\CompanyRequest;
use InvoiceShelf\Http\Requests\ProfileRequest;
use InvoiceShelf\Http\Resources\CompanyResource;
use InvoiceShelf\Http\Resources\UserResource;
use InvoiceShelf\Models\Company;

class CompanyController extends Controller
{
    /**
     * Retrive the Admin account.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the Admin profile.
     * Includes name, email and (or) password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(ProfileRequest $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Update Admin Company Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(CompanyRequest $request)
    {
        $company = Company::find($request->header('company'));

        $this->authorize('manage company', $company);

        $company->update($request->getCompanyPayload());

        $company->address()->updateOrCreate(['company_id' => $company->id], $request->address);

        return new CompanyResource($company);
    }

    /**
     * Upload the company logo to storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyLogo(CompanyLogoRequest $request)
    {
        $company = Company::find($request->header('company'));

        $this->authorize('manage company', $company);

        $data = json_decode($request->company_logo);

        if (isset($request->is_company_logo_removed) && (bool) $request->is_company_logo_removed) {
            $company->clearMediaCollection('logo');
        }
        if ($data) {
            $company = Company::find($request->header('company'));

            if ($company) {
                $company->clearMediaCollection('logo');

                $company->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('logo');
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Upload the Admin Avatar to public storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(AvatarRequest $request)
    {
        $user = auth()->user();

        if (isset($request->is_admin_avatar_removed) && (bool) $request->is_admin_avatar_removed) {
            $user->clearMediaCollection('admin_avatar');
        }
        if ($user && $request->hasFile('admin_avatar')) {
            $user->clearMediaCollection('admin_avatar');

            $user->addMediaFromRequest('admin_avatar')
                ->toMediaCollection('admin_avatar');
        }

        if ($user && $request->has('avatar')) {
            $data = json_decode($request->avatar);
            $user->clearMediaCollection('admin_avatar');

            $user->addMediaFromBase64($data->data)
                ->usingFileName($data->name)
                ->toMediaCollection('admin_avatar');
        }

        return new UserResource($user);
    }
}
