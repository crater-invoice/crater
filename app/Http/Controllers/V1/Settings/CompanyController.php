<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CompanyRequest;
use Crater\Http\Requests\ProfileRequest;
use Crater\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Retrive the Admin account.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        $user = Auth::user();

        $user->load([
            'addresses',
            'addresses.country',
            'company',
            'company.address',
            'company.address.country',
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Update the Admin profile.
     * Includes name, email and (or) password
     *
     * @param  \Crater\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->update($request->validated());

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    /**
     * Update Admin Company Details
     * @param \Crater\Http\Requests\CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(CompanyRequest $request)
    {
        $company = Auth::user()->company;

        $company->update($request->only('name'));

        $company->address()->updateOrCreate(['company_id' => $company->id], $request->except(['name']));

        return response()->json([
            'company' => $company,
            'success' => true,
        ]);
    }

    /**
     * Upload the company logo to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyLogo(Request $request)
    {
        $data = json_decode($request->company_logo);

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(Request $request)
    {
        $data = json_decode($request->admin_avatar);

        if ($data) {
            $user = auth()->user();

            if ($user) {
                $user->clearMediaCollection('admin_avatar');

                $user->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('admin_avatar');
            }
        }

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }
}
