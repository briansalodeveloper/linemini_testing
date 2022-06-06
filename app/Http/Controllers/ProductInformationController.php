<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ContentPlanRequest;
use App\Services\ContentPlanService;
use App\Models\ContentPlan;

class ProductInformationController extends Controller
{
    /**
     * @param ContentPlanService $service
     * @return void
     */
    public function __construct(ContentPlanService $service)
    {
        $this->service = $service;
    }

    /**
     * product information list
     *
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->all(ContentPlan::CONTENTTYPE_PRODUCTINFO);
        return view('page.productInformation.index')->with($data);
    }

    /**
     * product information create form
     * @return View
     */
    public function create(): View
    {
        return $this->edit();
    }

    /**
     * product information edit form
     *
     * @param int|null $id
     * @return View
     */
    public function edit(int $id = null): View
    {
        $data = $this->service->get($id);
        return view('page.productInformation.detail')->with($data);
    }

    /**
     * product information request store
     *
     * @param ContentPlanRequest $request
     * @return RedirectResponse
     */
    public function store(ContentPlanRequest $request): RedirectResponse
    {
        $rtn = $this->service->store(ContentPlan::CONTENTTYPE_PRODUCTINFO);

        if ($rtn) {
            if ($request->has('cp')) {
                return redirect()
                    ->route('productInformation.edit', $rtn->id)
                    ->with('success', __('messages.success.create', ['name' => __('words.ProductInformation')]));
            } else {
                return redirect()
                    ->route('productInformation.index')
                    ->with('success', __('messages.success.create', ['name' => __('words.ProductInformation')]));
            }
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.create', ['name' => __('words.ProductInformation')]));
        }
    }

    /**
     * product information request update
     *
     * @param ContentPlanRequest $request
     * @param Int $id
     * @return RedirectResponse
     */
    public function update(ContentPlanRequest $request, int $id): RedirectResponse
    {
        $rtn = $this->service->update($id, ContentPlan::CONTENTTYPE_PRODUCTINFO);

        if ($rtn) {
            return redirect()
                ->route('productInformation.index')
                ->with('success', __('messages.success.update', ['name' => __('words.ProductInformation')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.update', ['name' => __('words.ProductInformation')]));
        }
    }

    /**
     * product information request destroy
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $rtn = $this->service->destroy($id);

        if ($rtn) {
            return redirect()
                ->route('productInformation.index')
                ->with('success', __('messages.success.delete', ['name' => __('words.ProductInformation')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.delete', ['name' => __('words.ProductInformation')]));
        }
    }

    /**
     * upload images or csv
     *
     * @param Request $request
     * @return JsonResponse []
     */
    public function upload(Request $request): JsonResponse
    {
        $rtn = false;
        $url = '';
        $file = '';
        $fileType = '';
        $uploadType = $request->get('uploadType', '');

        if ($uploadType == 'thumbnail') {
            if ($request->has('thumbnail')) {
                $fileType = 'image';
                $file = $request->file('thumbnail');
            }
        } elseif ($uploadType == 'csv') {
            if ($request->has('csv')) {
                $fileType = 'csv';
                $file = $request->file('csv');
            }
        }

        $rtn = $this->service->upload($file, $fileType);

        if (!empty($rtn)) {
            $url = $rtn;
        }

        return response()->json([
            'success' => !!$rtn,
            'url' => $url
        ]);
    }

    /**
     * upload trumbowyg images
     *
     * @param Request $request
     * @return JsonResponse []
     */
    public function uploadTrumbowygImage(Request $request): JsonResponse
    {
        $rtn = false;
        $link = $this->service->upload($request->image, 'image');

        if (!empty($link)) {
            $rtn = true;
        }

        return response()->json([
            'success' => $rtn,
            'url' => $link
        ]);
    }
}
